/*
 * Copyright (C) 2014, Jason Rush
 */
#include "doors.h"

#include <stdio.h>
#include <string.h>
#include <syslog.h>

#include "door.h"

static door_t *doors[MAX_DOORS];

int doors_init(config_t *config, event_mgr_t *event_mgr) {
  char prefix[10];
  char str[128];
  int i;

  memset(doors, 0, sizeof(doors));

  for (i = 0; i < MAX_DOORS; i++) {
    snprintf(prefix, sizeof(prefix), "door%d", i);

    // Check if this door exists
    snprintf(str, sizeof(str), "%s.enabled", prefix);
    if (config_get_boolean(config, str, 0) != 1) {
      continue;
    }

    // Allocate and configure the door
    doors[i] = door_alloc(config, prefix, event_mgr);
    if (doors[i] == NULL) {
      return -1;
    }
  }

  return 0;
}

void doors_release(void) {
  int i;

  for (i = 0; i < MAX_DOORS; i++) {
    door_release(doors[i]);
    doors[i] = NULL;
  }
}

door_t* doors_get_door(int index) {
  return doors[index];
}

