/*
 * Copyright (C) 2014, Jason Rush
 */
#ifndef __NOTIFICATIONS_H__
#define __NOTIFICATIONS_H__

#include "config.h"

int notifications_init(config_t *config);
void notifications_release(void);

void notifications_notify(const char *door_name);

#endif
