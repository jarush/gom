/*
 * Copyright (C) 2013, Jason Rush
 */
#include "gpio_mgr.h"

#include <stdio.h>
#include <string.h>
#include <syslog.h>

#include "action.h"

static gpio_t *gpios[MAX_GPIO];

int gpio_mgr_init(config_t *config) {
  memset(gpios, 0, sizeof(gpios));

  if (gpio_mgr_load_config(config) == -1) {
    return -1;
  }

  return 0;
}

void gpio_mgr_release(void) {
  int i;

  for (i = 0; i < MAX_GPIO; i++) {
    gpio_release(gpios[i]);
    gpios[i] = NULL;
  }
}

int gpio_mgr_load_config(config_t *config) {
  char str[1024];
  int i;

  for (i = 0; i < MAX_GPIO; i++) {
    // Parse the direction
    snprintf(str, sizeof(str), "gpio%d.direction", i);
    int direction = config_get_int32(config, str, -1);
    if (direction == -1) {
      continue;
    }

    // Allocate the gpio
    gpios[i] = gpio_alloc(i);
    if (gpios[i] == NULL) {
      return -1;
    }

    // Configure it as either an input or output
    if (direction == GPIO_DIR_INPUT) {
      // Parse the polling interval
      snprintf(str, sizeof(str), "gpio%d.interval.sec", i);
      int sec = config_get_int32(config, str, -1);
      snprintf(str, sizeof(str), "gpio%d.interval.nsec", i);
      int nsec = config_get_int32(config, str, -1);
      if (sec < 0 || nsec < 0) {
        syslog(LOG_ERR, "Invalid interval (%ds %dns) for gpio%d", sec, nsec, i);
        gpio_release(gpios[i]);
        gpios[i] = NULL;
        return -1;
      }

      // Parse if this GPIO is active low
      snprintf(str, sizeof(str), "gpio%d.active_low", i);
      int active_low = config_get_int32(config, str, 0);

      // Parse the GPIO trigger timer
      snprintf(str, sizeof(str), "gpio%d.trigger_time", i);
      double trigger_time = config_get_double(config, str, 0.0);

      // Parse the action to perform
      snprintf(str, sizeof(str), "gpio%d.action", i);
      action_t *action = action_alloc(config, str);
      if (action == NULL) {
        syslog(LOG_ERR, "Failed to create action");
        gpio_release(gpios[i]);
        gpios[i] = NULL;
        return -1;
      }

      // Configure the GPIO as an input
      if (gpio_set_input(gpios[i], sec, nsec,
            active_low, trigger_time, action) == -1) {
        gpio_release(gpios[i]);
        gpios[i] = NULL;
        return -1;
      }
    } else {
      // Configure the GPIO as an output
      if (gpio_set_output(gpios[i]) == -1) {
        gpio_release(gpios[i]);
        gpios[i] = NULL;
        return -1;
      }
    }
  }

  return 0;
}

gpio_t* gpio_mgr_get(int index) {
  return gpios[index];
}

