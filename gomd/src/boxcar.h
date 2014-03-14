/*
 * Copyright (C) 2014, Jason Rush
 */
#ifndef __BOXCAR_H__
#define __BOXCAR_H__

#include "config.h"

typedef struct {
  char access_token[128];
  char sound[128];
} boxcar_t;

boxcar_t* boxcar_alloc(config_t *config);
void boxcar_release(boxcar_t *boxcar);

int boxcar_notify(boxcar_t *boxcar, const char *title, const char *message);

#endif
