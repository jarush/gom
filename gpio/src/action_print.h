/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __ACTION_PRINT_H__
#define __ACTION_PRINT_H__

#include "action.h"

typedef struct {
  action_t parent;
} action_print_t;

action_print_t* action_print_alloc(config_t *config, const char *prefix);

#endif
