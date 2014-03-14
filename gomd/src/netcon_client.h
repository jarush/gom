/*
 * Copyright (C) 2014, Jason Rush
 */
#ifndef __NETCON_CLIENT_H__
#define __NETCON_CLIENT_H__

#include "event_mgr.h"

#define MAX_BUFFER_SIZE 1024

typedef struct {
  int fd;
  char buffer[MAX_BUFFER_SIZE + 1];
  int nbytes;
} netcon_client_t;

netcon_client_t* netcon_client_alloc(int fd);
void netcon_client_release(netcon_client_t *netcon_client);

int netcon_client_send(netcon_client_t *netcon_client, const char *response);

int netcon_client_process(event_mgr_t *event_mgr, int fd, void *data);

#endif
