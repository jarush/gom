/*
 * Copyright (C) 2013, Jason Rush
 */
#include "action_print.h"

#include <stdio.h>
#include <stdlib.h>

static int action_print_callback(void *action_ptr, int value);

action_print_t* action_print_alloc(config_t *config, const char *prefix) {
  action_print_t *action;

  // Allocate the structure
  action = (action_print_t*)malloc(sizeof(action_print_t));
  if (action == NULL) {
    fprintf(stderr, "Failed to allocate structure\n");
    return NULL;
  }

  // Initialize the structure
  action->parent.callback = action_print_callback;

  return action;
}

static int action_print_callback(void *action_ptr, int value) {
  printf("GPIO %d\n", value);

  return 0;
}

