/*
 * Copyright (C) 2014, Jason Rush
 */
#include "event_mgr.h"

#include <stdio.h>
#include <stdlib.h>
#include <errno.h>
#include <assert.h>
#include <unistd.h>
#include <syslog.h>

void event_mgr_init(event_mgr_t *event_mgr) {
  // Initialize the event handler list
  list_init(&event_mgr->event_handlers);
}

void event_mgr_release(event_mgr_t *event_mgr) {
  list_item_t *item;
  event_handler_t *event_handler;

  // Close all the event handler file descriptors
  for (item = list_first(&event_mgr->event_handlers);
      item != NULL; item = item->next) {
    event_handler = (event_handler_t*)item->data;

    // Close the file descriptor
    close(event_handler->fd);
  }

  // Free the list and event handlers
  list_release_data(&event_mgr->event_handlers);
}

void event_mgr_add(event_mgr_t *event_mgr, int fd,
    event_callback_fn callback, void *data) {
  event_handler_t *event_handler;

  // Allocate the event handler
  event_handler = malloc(sizeof(event_handler_t));
  assert(event_handler != NULL);

  // Initialize the event handler
  event_handler->fd = fd;
  event_handler->callback = callback;
  event_handler->data = data;

  // Add the event handler to the list
  list_append(&event_mgr->event_handlers, event_handler);
}

int event_mgr_remove(event_mgr_t *event_mgr, int fd) {
  list_item_t *item;
  event_handler_t *event_handler;

  // Find the event handler
  for (item = list_first(&event_mgr->event_handlers);
      item != NULL; item = item->next) {
    event_handler = (event_handler_t*)item->data;
    if (event_handler->fd == fd) {
      // Delete the event handler and remove the item from the list
      free(event_handler);
      list_remove_item(&event_mgr->event_handlers, item);
      return 0;
    }
  }

  return -1;
}

int event_mgr_process(event_mgr_t *event_mgr) {
  list_item_t *item;
  list_item_t *next_item;
  event_handler_t *event_handler;
  fd_set fds;
  int nfds = 0;

  // Zero out the fd_set
  FD_ZERO(&fds);

  // Add the event handler file descriptors
  for (item = list_first(&event_mgr->event_handlers);
      item != NULL; item = item->next) {
    event_handler = (event_handler_t*)item->data;

    // Add the file descriptor to the set
    FD_SET(event_handler->fd, &fds);
    nfds = event_handler->fd > nfds ? event_handler->fd : nfds;
  }

  // Call select
  if (select(nfds + 1, &fds, NULL, NULL, NULL) == -1) {
    if (errno == EINTR) {
      return 0;
    } else {
      syslog(LOG_ERR, "Error performing select: %m");
      return -1;
    }
  }

  // Check the event handler file descriptors
  item = list_first(&event_mgr->event_handlers);
  while (item != NULL) {
    event_handler = (event_handler_t*)item->data;
    next_item = item->next;

    // Check the file descriptor is set
    if (FD_ISSET(event_handler->fd, &fds)) {
      // Invoke the event handler's callback
      if (event_handler->callback(event_mgr,
            event_handler->fd, event_handler->data) == -1) {
        // Delete the event handler and remove the item from the list
        free(event_handler);
        list_remove_item(&event_mgr->event_handlers, item);
      }
    }

    item = next_item;
  }

  return 0;
}
