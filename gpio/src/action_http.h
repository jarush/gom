/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __ACTION_HTTP_H__
#define __ACTION_HTTP_H__

#include "action.h"

typedef struct {
  action_t parent;
  char url[1024];
} action_http_t;

action_http_t* action_http_alloc(config_t *config, const char *prefix);

#endif
