/*
 * Copyright (C) 2013, Jason Rush
 */
#include "netcon.h"
#include "netcon_client.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
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
    fprintf(stderr, "Failed to allocate structure\n");
    return NULL;
  }

  // Initialize the structure
  netcon->fd = -1;

  // Create the socket
  if ((netcon->fd = socket(AF_INET, SOCK_STREAM, 0)) == -1) {
    perror("socket");
    netcon_release(netcon);
    return NULL;
  }

  // Set SO_REUSEADDR to true
  tmp = 1;
  if (setsockopt(netcon->fd, SOL_SOCKET, SO_REUSEADDR,
      &tmp, sizeof(tmp)) == -1) {
    perror("setsockopt");
    netcon_release(netcon);
    return NULL;
  }

  // Initialize the bind address
  memset(&addr, 0, sizeof(addr));
  addr.sin_port = htons(port);
  addr.sin_family = AF_INET;

  // Bind the socket to the address
  if (bind(netcon->fd, (struct sockaddr *)&addr, sizeof(addr)) == -1) {
    perror("bind");
    netcon_release(netcon);
    return NULL;
  }

  // Start listening for new connections
  if (listen(netcon->fd, 5) == -1) {
    perror("listen");
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
    perror("accept");
    return -1;
  }

  printf("Connection from %s:%d\n", inet_ntoa(addr.sin_addr), ntohs(addr.sin_port));

  // Allocate a new network console client
  if ((netcon_client = netcon_client_alloc(sd)) == NULL) {
    fprintf(stderr, "Failed to allocate new client connection\n");
    return 0;
  }

  // Add an event handler for the new client to the event manager
  event_mgr_add(event_mgr, sd, netcon_client_process, netcon_client);

  return 0;
}
