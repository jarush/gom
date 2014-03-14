/*
 * Copyright (C) 2014, Jason Rush
 */
#include <stdio.h>
#include <stdlib.h>
#include <assert.h>
#include <unistd.h>
#include <syslog.h>

#include "config.h"
#include "event_mgr.h"
#include "netcon.h"
#include "notifications.h"
#include "doors.h"

typedef struct {
  int dont_fork;
  int log_stderr;
  const char *filename;
} options_t;

void usage(const char *error_message, int return_value) {
  if (error_message != NULL) {
    fprintf(stderr, "%s\n\n", error_message);
  }

  fprintf(stderr, "Usage: gpiod [-d -e] <config file>\n");
  fprintf(stderr, "\n");
  fprintf(stderr, "Options:\n");
  fprintf(stderr, "  -d  Don't fork into the background\n");
  fprintf(stderr, "  -e  Log error messages to stderr as well as syslog\n");
  fprintf(stderr, "  -h  Display this help\n");

  exit(return_value);
}

void parse_options(char *argv[], int argc, options_t *options) {
  int opt;

  options->dont_fork = 0;
  options->log_stderr = 0;
  options->filename = NULL;

  while ((opt = getopt(argc, argv, "+deh")) != -1) {
    switch (opt) {
      case 'd':
        options->dont_fork = 1;
        break;

      case 'e':
        options->log_stderr = 1;
        break;

      case 'h':
        usage(NULL, 0);
        break;

      case '?':
      default:
        usage("Error parsing command line arguments", 1);
        return;
    }
  }

  if (optind >= argc) {
    usage("Missing required <config file> argument", 1);
    return;
  }

  options->filename = argv[optind];

  return;
}

int main(int argc, char *argv[]) {
  options_t options;
  config_t config;
  event_mgr_t event_mgr;
  netcon_t *netcon;

  // Parse the command line options
  parse_options(argv, argc, &options);

  // Open syslog
  int log_flags = options.log_stderr ? LOG_PERROR : 0;
  openlog("gpiod", LOG_CONS | LOG_PID | LOG_NDELAY | log_flags, LOG_DAEMON);
  syslog(LOG_INFO, "Program started");

  // Initialize and read the configuration file
  config_init(&config);
  if (config_read(&config, options.filename) == -1) {
    return 1;
  }

  // Check if we shouldn't fork into the background
  if (options.dont_fork == 0) {
    // Fork into the background
    if (daemon(0, 0) == -1) {
      syslog(LOG_ERR, "Error forking daemon: %m");
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
  syslog(LOG_INFO, "Network console listening on port %d", port);

  // Add the network console to the event manager
  event_mgr_add(&event_mgr, netcon->fd, netcon_process, netcon);

  // Initialize the notifications
  if (notifications_init(&config) == -1) {
    return 1;
  }

  // Initialize the doors
  if (doors_init(&config, &event_mgr) == -1) {
    return 1;
  }

  syslog(LOG_INFO, "Starting event loop");

  // Run the event manager
  while (event_mgr_process(&event_mgr) != -1);

  // Cleanup the event manager (cleans up all handlers)
  event_mgr_release(&event_mgr);

  // Cleanup the doors
  doors_release();

  // Cleanup the notifications
  notifications_release();

  // Close syslog
  closelog();

  return 0;
}
