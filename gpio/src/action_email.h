/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __ACTION_EMAIL_H__
#define __ACTION_EMAIL_H__

#include "action.h"

typedef struct {
  action_t parent;
  char url[256];
  char from[256];
  char to[256];
  char username[128];
  char password[128];
  char message[256];
} action_email_t;

action_email_t* action_email_alloc(config_t *config, const char *prefix);

#endif
