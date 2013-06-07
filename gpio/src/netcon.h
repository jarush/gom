/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __NETCON_H__
#define __NETCON_H__

#include "event_mgr.h"

typedef struct {
  int fd;
} netcon_t;

netcon_t* netcon_alloc(unsigned short port);
void netcon_release(netcon_t *netcon);

int netcon_process(event_mgr_t *event_mgr, int fd, void *data);

#endif
