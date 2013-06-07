/*
 * Copyright (C) 2013, Jason Rush
 */
#include "gpio.h"

#include <stdio.h>
#include <stdint.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/timerfd.h>
#include <bcm2835.h>

static int initialized = 0;

gpio_t* gpio_alloc(int pin) {
  gpio_t *gpio;

  // Allocate the structure
  gpio = (gpio_t*)malloc(sizeof(gpio_t));
  if (gpio == NULL) {
    fprintf(stderr, "Failed to allocate structure\n");
    return NULL;
  }

  // Initialize the structure
  gpio->pin = pin;
  gpio->fd = -1;
  gpio->active_low = 0;
  gpio->previous_value = -1;
  gpio->action = NULL;

  // Initialize the BCM2835 library
  if (!initialized) {
    if (!bcm2835_init()) {
      fprintf(stderr, "Failed to initialize the BCM2835 library\n");
      gpio_release(gpio);
      return NULL;
    }
    initialized = 1;
  }

  // Default the pin to input
  bcm2835_gpio_fsel(pin, BCM2835_GPIO_FSEL_INPT);

  return gpio;
}

int gpio_set_input(gpio_t *gpio, int sec, int nsec,
    int active_low, action_t *action) {
  struct itimerspec new_value;

  // Initialize the structure
  gpio->direction = GPIO_DIR_INPUT;
  gpio->active_low = active_low;
  gpio->action = action;

  // Configure the pin as an input
  bcm2835_gpio_fsel(gpio->pin, BCM2835_GPIO_FSEL_INPT);

  // Create the timer fd
  gpio->fd = timerfd_create(CLOCK_MONOTONIC, 0);
  if (gpio->fd == -1) {
    perror("timerfd_create");
    gpio_release(gpio);
    return -1;
  }

  // Initialize the new timer
  new_value.it_value.tv_sec = sec;
  new_value.it_value.tv_nsec = nsec;
  new_value.it_interval.tv_sec = sec;
  new_value.it_interval.tv_nsec = nsec;

  // Set the timer
  if (timerfd_settime(gpio->fd, 0, &new_value, NULL) == -1) {
    perror("timerfd_settime");
    gpio_release(gpio);
    return -1;
  }

  return 0;
}

int gpio_set_output(gpio_t *gpio) {
  // Initialize the structure
  gpio->direction = GPIO_DIR_OUTPUT;

  // Configure the pin as an input
  bcm2835_gpio_fsel(gpio->pin, BCM2835_GPIO_FSEL_OUTP);

  return 0;
}

void gpio_release(gpio_t *gpio) {
  if (gpio->action != NULL) {
    action_release(gpio->action);
  }

  if (gpio->fd != -1) {
    close(gpio->fd);
  }

  free(gpio);
}

int gpio_read(gpio_t *gpio) {
  int value = bcm2835_gpio_lev(gpio->pin);

  // Invert the value if it's active low
  if (gpio->active_low) {
    value = !value;
  }

  return value;
}

void gpio_set(gpio_t *gpio) {
  bcm2835_gpio_set(gpio->pin);
}

void gpio_clr(gpio_t *gpio) {
  bcm2835_gpio_clr(gpio->pin);
}

int gpio_process(event_mgr_t *event_mgr, int fd, void *data) {
  gpio_t *gpio = (gpio_t*)data;
  uint64_t exp;
  int value;

  // Read the timer
  read(gpio->fd, &exp, sizeof(exp));

  // Read the GPIO value
  value = gpio_read(gpio);

  // Check if the GPIO value has changed
  if (gpio->previous_value == -1 || value != gpio->previous_value) {
    if (gpio->action->callback(gpio->action, value) == -1) {
      fprintf(stderr, "Failed to invoke action callback\n");
    }
    gpio->previous_value = value;
  }

  return 0;
}

