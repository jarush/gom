/*
 * Copyright (C) 2013, Jason Rush
 */
#include "netcon.h"
#include "netcon_client.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <syslog.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

netcon_t* netcon_alloc(unsigned short port) {
  netcon_t *netcon;
  struct sockaddr_in addr;
  int tmp;

  // Allocate the structure
  netcon = (netcon_t*)malloc(sizeof(netcon_t));
  if (netcon == NULL) {
    syslog(LOG_ERR, "Failed to allocate structure");
    return NULL;
  }

  // Initialize the structure
  netcon->fd = -1;

  // Create the socket
  if ((netcon->fd = socket(AF_INET, SOCK_STREAM, 0)) == -1) {
    syslog(LOG_ERR, "Error creating socket: %m");
    netcon_release(netcon);
    return NULL;
  }

  // Set SO_REUSEADDR to true
  tmp = 1;
  if (setsockopt(netcon->fd, SOL_SOCKET, SO_REUSEADDR,
      &tmp, sizeof(tmp)) == -1) {
    syslog(LOG_ERR, "Error setting SO_REUSEADDR: %m");
    netcon_release(netcon);
    return NULL;
  }

  // Initialize the bind address
  memset(&addr, 0, sizeof(addr));
  addr.sin_port = htons(port);
  addr.sin_family = AF_INET;

  // Bind the socket to the address
  if (bind(netcon->fd, (struct sockaddr *)&addr, sizeof(addr)) == -1) {
    syslog(LOG_ERR, "Error binding socket: %m");
    netcon_release(netcon);
    return NULL;
  }

  // Start listening for new connections
  if (listen(netcon->fd, 5) == -1) {
    syslog(LOG_ERR, "Error starting to listen on socket: %m");
    netcon_release(netcon);
    return NULL;
  }

  return netcon;
}

void netcon_release(netcon_t *netcon) {
  if (netcon->fd != -1) {
    close(netcon->fd);
  }

  free(netcon);
}

int netcon_process(event_mgr_t *event_mgr, int fd, void *data) {
  netcon_client_t *netcon_client;
  struct sockaddr_in addr;
  socklen_t addr_len;
  int sd;

  // Initialize the address
  addr_len = sizeof(addr);
  memset(&addr, 0, addr_len);

  // Accept the connection
  if ((sd = accept(fd, (struct sockaddr *)&addr, &addr_len)) == -1) {
    syslog(LOG_ERR, "Error accepting client connection: %m");
    return -1;
  }

  syslog(LOG_INFO, "Connection from %s:%d", inet_ntoa(addr.sin_addr), ntohs(addr.sin_port));

  // Allocate a new network console client
  if ((netcon_client = netcon_client_alloc(sd)) == NULL) {
    syslog(LOG_ERR, "Failed to allocate new client connection");
    return 0;
  }

  // Add an event handler for the new client to the event manager
  event_mgr_add(event_mgr, sd, netcon_client_process, netcon_client);

  return 0;
}
