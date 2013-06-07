/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __ACTION_H__
#define __ACTION_H__

#include "config.h"

typedef int (*action_callback_fn)(void *action_ptr, int value);

typedef struct {
  action_callback_fn callback;
} action_t;

action_t* action_alloc(config_t *config, const char *prefix);
void action_release(action_t *action);

#endif
