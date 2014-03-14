/*
 * Copyright (C) 2014, Jason Rush
 */
#ifndef __EMAIL_H__
#define __EMAIL_H__

#include "config.h"

typedef struct {
  char url[256];
  char from[256];
  char to[256];
  char username[128];
  char password[128];
} email_t;

email_t* email_alloc(config_t *config);
void email_release(email_t *email);

int email_notify(email_t *email, const char *title, const char *message);

#endif
