/*
 * Copyright (C) 2014, Jason Rush
 */
#include "notifications.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <syslog.h>

#include "boxcar.h"
#include "email.h"

static boxcar_t *boxcar = NULL;
static email_t *email = NULL;

int notifications_init(config_t *config) {
  // Configure boxcar notifications if enabled
  if (config_get_boolean(config, "boxcar.enabled", 0)) {
    boxcar = boxcar_alloc(config);
    if (boxcar == NULL) {
      return -1;
    }
  }

  // Configure email notifications if enabled
  if (config_get_boolean(config, "email.enabled", 0)) {
    email = email_alloc(config);
    if (email == NULL) {
      return -1;
    }
  }

  return 0;

}

void notifications_release(void) {
  if (boxcar != NULL) {
    boxcar_release(boxcar);
  }

  if (email != NULL) {
    email_release(email);
  }
}

void notifications_notify(const char *door_name) {
  const char *title = "Garage Door Open";

  char message[128];
  snprintf(message, sizeof(message), "%s Door is Open", door_name);

  syslog(LOG_INFO, "%s", message);

  if (boxcar != NULL) {
    boxcar_notify(boxcar, title, message);
  }

  if (email != NULL) {
    email_notify(email, title, message);
  }
}

