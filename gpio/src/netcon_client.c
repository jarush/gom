/*
 * Copyright (C) 2013, Jason Rush
 */
#include "netcon_client.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#include "command_process.h"

netcon_client_t* netcon_client_alloc(int fd) {
  netcon_client_t *netcon_client;

  // Allocate the structure
  netcon_client = (netcon_client_t*)malloc(sizeof(netcon_client_t));
  if (netcon_client == NULL) {
    fprintf(stderr, "Failed to allocate structure\n");
    return NULL;
  }

  // Initialize the structure
  memset(netcon_client, 0, sizeof(netcon_client_t));
  netcon_client->fd = fd;

  return netcon_client;
}

void netcon_client_release(netcon_client_t *netcon_client) {
  if (netcon_client->fd != -1) {
    close(netcon_client->fd);
  }

  free(netcon_client);
}

int netcon_client_send(netcon_client_t *netcon_client, const char *response) {
  int len = strlen(response);

  if (write(netcon_client->fd, response, len) != len) {
    perror("write");
    return -1;
  }

  return 0;
}

int netcon_client_process(event_mgr_t *event_mgr, int fd, void *data) {
  netcon_client_t *netcon_client = (netcon_client_t*)data;
  char *ptr1;
  char *ptr2;
  int n;

  // Compute how many bytes can be read
  n = sizeof(netcon_client->buffer) - netcon_client->nbytes;

  // Read from the client
  n = read(fd, netcon_client->buffer + netcon_client->nbytes, n);
  if (n == 0) {
    // The connection is closed
    return -1;
  } else if (n == -1) {
    // An error occured
    perror("read");
    return -1;
  }

  netcon_client->nbytes += n;

  // Null terminate the string
  netcon_client->buffer[netcon_client->nbytes] = '\0';

  // Parse lines of text
  ptr1 = netcon_client->buffer;
  while ((ptr2 = strchr(ptr1, '\n')) != NULL) {
    // Replace the newline with a null
    *ptr2 = '\0';

    // Replace a carage return with a null if it exists
    if ((ptr2 - 1) >= ptr1 && *(ptr2 - 1) == '\r') {
      *(ptr2 - 1) = '\0';
    }

    // Process the command
    if (*ptr1 != '\0') {
      command_process(netcon_client, ptr1);
    }

    // Move the pointer past this command
    ptr1 = ptr2 + 1;
  }

  // Compact the buffer
  netcon_client->nbytes -= (ptr1 - netcon_client->buffer);
  memcpy(netcon_client->buffer, ptr1, netcon_client->nbytes);

  return 0;
}

