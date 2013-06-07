/*
 * Copyright (C) 2013, Jason Rush
 */
#include "action.h"
#include "action_print.h"
#include "action_http.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

action_t* action_alloc(config_t *config, const char *prefix) {
  action_t *action;
  char str[1024];

  // Get the action type
  snprintf(str, sizeof(str), "%s.type", prefix);
  const char *type = config_get_string(config, str, "");

  // Parse the action
  if (strcmp(type, "print") == 0) {
    action = (action_t*)action_print_alloc(config, prefix);
  } else if (strcmp(type, "http") == 0) {
    action = (action_t*)action_http_alloc(config, prefix);
  } else {
    fprintf(stderr, "Invalid action type: %s\n", type);
    return NULL;
  }

  return action;

}

void action_release(action_t *action) {
  free(action);
}

