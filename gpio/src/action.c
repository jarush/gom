/*
 * Copyright (C) 2013, Jason Rush
 */
#include "action.h"
#include "action_print.h"
#include "action_tweet.h"
#include "action_email.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <syslog.h>

action_t* action_alloc(config_t *config, const char *prefix) {
  action_t *action;
  char str[1024];

  // Get the action type
  snprintf(str, sizeof(str), "%s.type", prefix);
  const char *type = config_get_string(config, str, "");

  // Parse the action
  if (strcmp(type, "print") == 0) {
    action = (action_t*)action_print_alloc(config, prefix);
  } else if (strcmp(type, "tweet") == 0) {
    action = (action_t*)action_tweet_alloc(config, prefix);
  } else if (strcmp(type, "email") == 0) {
    action = (action_t*)action_email_alloc(config, prefix);
  } else {
    syslog(LOG_ERR, "Invalid action type: %s", type);
    return NULL;
  }

  return action;

}

void action_release(action_t *action) {
  free(action);
}

