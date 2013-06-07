/*
 * Copyright (C) 2013, Jason Rush
 */
#include <stdio.h>
#include <stdlib.h>
#include <assert.h>
#include <unistd.h>

#include "config.h"
#include "event_mgr.h"
#include "netcon.h"
#include "gpio_mgr.h"

int main(int argc, char *argv[]) {
  config_t config;
  event_mgr_t event_mgr;
  netcon_t *netcon;
  int i;

  if (argc != 2) {
    fprintf(stderr, "Usage: %s <config file>\n", argv[0]);
    return 1;
  }

  // Initialize the configuration
  config_init(&config);

  // Read the config file
  if (config_read(&config, argv[1]) == -1) {
    return 1;
  }

  // Check if we should fork into a background process
  if (config_get_int32(&config, "daemon", 0) != 0) {
    // Fork into the background
    if (daemon(0, 0) == -1) {
      perror("daemon");
      return 1;
    }
  }

  // Initialize the event manager
  event_mgr_init(&event_mgr);

  // Initialize the network console
  int port = config_get_uint32(&config, "netcon.port", 6965);
  if ((netcon = netcon_alloc(port)) == NULL) {
    return 1;
  }

  // Add the network console to the event manager
  event_mgr_add(&event_mgr, netcon->fd, netcon_process, netcon);

  // Initialize the GPIO manager
  if (gpio_mgr_init(&config) == -1) {
    return 1;
  }

  // Add all the GPIO inputs to the event manager
  for (i = 0; i < MAX_GPIO; i++) {
    // Get the GPIO
    gpio_t *gpio = gpio_mgr_get(i);
    if (gpio != NULL && gpio->direction == GPIO_DIR_INPUT) {
      // Add the event listener
      event_mgr_add(&event_mgr, gpio->fd, gpio_process, gpio);
    }
  }

  // Run the event manager
  while (event_mgr_process(&event_mgr) != -1);

  // Cleanup the event manager (cleans up all handlers)
  event_mgr_release(&event_mgr);

  // Cleanup the GPIOs
  gpio_mgr_release();

  return 0;
}
