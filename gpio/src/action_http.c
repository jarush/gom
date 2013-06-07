/*
 * Copyright (C) 2013, Jason Rush
 */
#include "action_http.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

static int action_http_callback(void *action_ptr, int value);

action_http_t* action_http_alloc(config_t *config, const char *prefix) {
  action_http_t *action;
  char str[1024];

  // Allocate the structure
  action = (action_http_t*)malloc(sizeof(action_http_t));
  if (action == NULL) {
    fprintf(stderr, "Failed to allocate structure\n");
    return NULL;
  }

  // Initialize the structure
  action->parent.callback = action_http_callback;

  // Get the URL
  snprintf(str, sizeof(str), "%s.url", prefix);
  const char *url = config_get_string(config, str, NULL);
  if (url == NULL) {
    fprintf(stderr, "Missing required parameter %s\n", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->url, url, sizeof(action->url));

  return action;
}

static int action_http_callback(void *action_ptr, int value) {
  action_http_t *action = (action_http_t*)action_ptr;

  printf("%s %d\n", action->url, value);

  return 0;
}

