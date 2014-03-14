/*
 * Copyright (C) 2014, Jason Rush
 */
#ifndef __DOOR_MGR_H__
#define __DOOR_MGR_H__

#include "door.h"
#include "config.h"

#define MAX_DOORS 10

int doors_init(config_t *config, event_mgr_t *event_mgr);
void doors_release(void);

door_t* doors_get_door(int index);

#endif

