/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __EVENT_MGR_H__
#define __EVENT_MGR_H__

#include "list.h"

typedef struct {
  list_t event_handlers;
} event_mgr_t;

typedef int (*event_callback_fn)(event_mgr_t *event_mgr, int fd, void *data);

typedef struct {
  int fd;
  event_callback_fn callback;
  void *data;
} event_handler_t;

void event_mgr_init(event_mgr_t *event_mgr);
void event_mgr_release(event_mgr_t *event_mgr);

void event_mgr_add(event_mgr_t *event_mgr, int fd,
  event_callback_fn callback, void *data);
int event_mgr_remove(event_mgr_t *event_mgr, int fd);

int event_mgr_process(event_mgr_t *event_mgr);

#endif
