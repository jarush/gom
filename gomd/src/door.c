/*
 * Copyright (C) 2014, Jason Rush
 */
#include "door.h"

#include <stdio.h>
#include <stdint.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <syslog.h>
#include <sys/timerfd.h>
#include <bcm2835.h>

#include "notifications.h"

static int initialized = 0;

static int sensor_set_timer(int sec, int nsec);
static int sensor_process(event_mgr_t *event_mgr, int fd, void *data);

static void timespec_sub(struct timespec *a,
    struct timespec *b, struct timespec *res);
double timespec_seconds(struct timespec *ts);

door_t* door_alloc(config_t *config, const char *prefix,
    event_mgr_t *event_mgr) {
  door_t *door;
  char str[1024];

  // Allocate the structure
  door = (door_t*)malloc(sizeof(door_t));
  if (door == NULL) {
    syslog(LOG_ERR, "Failed to allocate structure");
    return NULL;
  }

  // Initialize the structure
  door->sensor_timer_fd = -1;
  door->sensor_previous_value = -1;
  door->sensor_previous_time.tv_sec = 0;
  door->sensor_previous_time.tv_nsec = 0;

  // Get the name
  snprintf(str, sizeof(str), "%s.name", prefix);
  const char *name = config_get_string(config, str, NULL);
  if (name == NULL) {
    syslog(LOG_ERR, "Missing required parameter '%s'", str);
    door_release(door);
    return NULL;
  }
  strncpy(door->name, name, sizeof(door->name));

  // Get the sensor gpio
  snprintf(str, sizeof(str), "%s.sensor.gpio", prefix);
  door->sensor_gpio = config_get_int32(config, str, -1);
  if (door->sensor_gpio == -1) {
    syslog(LOG_ERR, "Missing required parameter '%s'", str);
    door_release(door);
    return NULL;
  }

  // Get the sensor active low
  snprintf(str, sizeof(str), "%s.sensor.active_low", prefix);
  door->sensor_active_low = config_get_int32(config, str, -1);
  if (door->sensor_active_low == -1) {
    syslog(LOG_ERR, "Missing required parameter '%s'", str);
    door_release(door);
    return NULL;
  }

  // Get the sensor interval seconds
  snprintf(str, sizeof(str), "%s.sensor.interval.sec", prefix);
  int sensor_interval_sec = config_get_int32(config, str, -1);
  if (sensor_interval_sec == -1) {
    syslog(LOG_ERR, "Missing required parameter '%s'", str);
    door_release(door);
    return NULL;
  }

  // Get the sensor interval nano-seconds
  snprintf(str, sizeof(str), "%s.sensor.interval.nsec", prefix);
  int sensor_interval_nsec = config_get_int32(config, str, -1);
  if (sensor_interval_nsec == -1) {
    syslog(LOG_ERR, "Missing required parameter '%s'", str);
    door_release(door);
    return NULL;
  }

  // Get the sensor notification delay
  snprintf(str, sizeof(str), "%s.sensor.notification_delay", prefix);
  door->notification_delay = config_get_double(config, str, -1);
  if (door->notification_delay == -1) {
    syslog(LOG_ERR, "Missing required parameter '%s'", str);
    door_release(door);
    return NULL;
  }

  // Get the relay gpio
  snprintf(str, sizeof(str), "%s.relay.gpio", prefix);
  door->relay_gpio = config_get_int32(config, str, -1);
  if (door->relay_gpio == -1) {
    syslog(LOG_ERR, "Missing required parameter '%s'", str);
    door_release(door);
    return NULL;
  }

  // Initialize the BCM2835 library
  if (!initialized) {
    if (!bcm2835_init()) {
      syslog(LOG_ERR, "Failed to initialize the BCM2835 library");
      door_release(door);
      return NULL;
    }
    initialized = 1;
  }

  // Configure the sensor gpio pin as an input
  bcm2835_gpio_fsel(door->sensor_gpio, BCM2835_GPIO_FSEL_INPT);

  // Configure the replay gpio pin as an output
  bcm2835_gpio_fsel(door->relay_gpio, BCM2835_GPIO_FSEL_OUTP);

  // Create the timer fd
  door->sensor_timer_fd = sensor_set_timer(sensor_interval_sec,
    sensor_interval_nsec);
  if (door->sensor_timer_fd == -1) {
    door_release(door);
    return NULL;
  }

  // Add the event listener
  event_mgr_add(event_mgr, door->sensor_timer_fd, sensor_process, door);

  return door;
}

void door_release(door_t *door) {
  if (door->sensor_timer_fd != -1) {
    close(door->sensor_timer_fd);
  }

  free(door);
}

int door_get_status(door_t *door) {
  int value = bcm2835_gpio_lev(door->sensor_gpio);

  // Invert the value if it's active low
  if (door->sensor_active_low) {
    value = !value;
  }

  return value;
}

void door_toggle(door_t *door) {
  bcm2835_gpio_set(door->relay_gpio);
  usleep(1000);
  bcm2835_gpio_clr(door->relay_gpio);
}

void timespec_sub(struct timespec *a,
    struct timespec *b, struct timespec *res) {
  res->tv_sec = a->tv_sec - b->tv_sec;
  res->tv_nsec = a->tv_nsec - b->tv_nsec;
  if (res->tv_nsec < 0) {
    res->tv_sec = res->tv_sec - 1;
    res->tv_nsec += 1000000000;
  }
}

double timespec_seconds(struct timespec *ts) {
  return ts->tv_sec + ts->tv_nsec / 1000000000.0;
}

int sensor_set_timer(int sec, int nsec) {
  int fd;

  // Create the timer fd
  fd = timerfd_create(CLOCK_MONOTONIC, 0);
  if (fd == -1) {
    syslog(LOG_ERR, "Error creating timer: %m");
    return -1;
  }

  // Initialize the new timer
  struct itimerspec new_value;
  new_value.it_value.tv_sec = sec;
  new_value.it_value.tv_nsec = nsec;
  new_value.it_interval.tv_sec = sec;
  new_value.it_interval.tv_nsec = nsec;

  // Set the timer
  if (timerfd_settime(fd, 0, &new_value, NULL) == -1) {
    syslog(LOG_ERR, "Error setting timer: %m");
    return -1;
  }

  return fd;
}

static int sensor_process(event_mgr_t *event_mgr, int fd, void *data) {
  door_t *door = (door_t*)data;
  struct timespec current_time;
  struct timespec delta_time;
  uint64_t exp;
  int value;

  // Read the timer
  read(door->sensor_timer_fd, &exp, sizeof(exp));

  // Read the GPIO value
  value = door_get_status(door);

  // Get the current time
  clock_gettime(CLOCK_MONOTONIC, &current_time);

  // Check if the sensor has changed
  if (value != door->sensor_previous_value) {
    door->sensor_previous_value = value;
    door->sensor_previous_time = current_time;
    door->notification_sent = 0;
  }

  // Check if we should send a notification that the door is open
  if (value == 1 && door->notification_sent == 0) {
    // Compute how long the GPIO has been active
    timespec_sub(&current_time, &door->sensor_previous_time, &delta_time);
    double delta_seconds = timespec_seconds(&delta_time);

    // Send a notification if we haven't already and enough time has ellapsed
    if (delta_seconds > door->notification_delay) {
      notifications_notify(door->name);
      door->notification_sent = 1;
    }
  }

  return 0;
}

