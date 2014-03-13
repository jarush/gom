/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __ACTION_BOXCAR_H__
#define __ACTION_BOXCAR_H__

#include "action.h"

typedef struct {
  action_t parent;
  char access_token[128];
  char sound[128];
  char title[256];
  char message[256];
} action_boxcar_t;

action_boxcar_t* action_boxcar_alloc(config_t *config, const char *prefix);

#endif
