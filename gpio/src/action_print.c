/*
 * Copyright (C) 2013, Jason Rush
 */
#include "action_print.h"

#include <stdio.h>
#include <stdlib.h>
#include <syslog.h>

static int action_print_callback(void *action_ptr, int value);

action_print_t* action_print_alloc(config_t *config, const char *prefix) {
  action_print_t *action;

  // Allocate the structure
  action = (action_print_t*)malloc(sizeof(action_print_t));
  if (action == NULL) {
    syslog(LOG_ERR, "Failed to allocate structure");
    return NULL;
  }

  // Initialize the structure
  action->parent.callback = action_print_callback;

  return action;
}

static int action_print_callback(void *action_ptr, int value) {
  syslog(LOG_INFO, "GPIO Value %d", value);

  return 0;
}

