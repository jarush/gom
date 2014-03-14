/*
 * Copyright (C) 2014, Jason Rush
 */
#ifndef __DOOR_H__
#define __DOOR_H__

#include <time.h>

#include "config.h"
#include "event_mgr.h"

typedef struct {
  char name[128];

  int sensor_gpio;
  int sensor_active_low;
  int sensor_timer_fd;
  int sensor_previous_value;
  struct timespec sensor_previous_time;

  double notification_delay;
  int notification_sent;

  int relay_gpio;
} door_t;

door_t* door_alloc(config_t *config, const char *prefix,
  event_mgr_t *event_mgr);
void door_release(door_t *door);

int door_get_status(door_t *door);
void door_toggle(door_t *door);

#endif
