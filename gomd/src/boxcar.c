/*
 * Copyright (C) 2014, Jason Rush
 */
#include "boxcar.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <syslog.h>
#include <curl/curl.h>

#define URL "https://new.boxcar.io/api/notifications"

boxcar_t* boxcar_alloc(config_t *config) {
  boxcar_t *boxcar;

  // Allocate the structure
  boxcar = (boxcar_t*)malloc(sizeof(boxcar_t));
  if (boxcar == NULL) {
    syslog(LOG_ERR, "Failed to allocate structure");
    return NULL;
  }

  // Get the access token
  const char *access_token = config_get_string(config,
    "boxcar.access_token", NULL);
  if (access_token == NULL) {
    syslog(LOG_ERR, "Missing required parameter 'boxcar.access_token'");
    boxcar_release(boxcar);
    return NULL;
  }
  strncpy(boxcar->access_token, access_token, sizeof(boxcar->access_token));

  // Get the sound
  const char *sound = config_get_string(config, "boxcar.sound", NULL);
  if (sound == NULL) {
    syslog(LOG_ERR, "Missing required parameter 'boxcar.sound'");
    boxcar_release(boxcar);
    return NULL;
  }
  strncpy(boxcar->sound, sound, sizeof(boxcar->sound));

  return boxcar;
}

void boxcar_release(boxcar_t *boxcar) {
  free(boxcar);
}

int boxcar_notify(boxcar_t *boxcar, const char *title, const char *message) {
  CURL *curl;
  CURLcode res;
  char str[1024];

  syslog(LOG_INFO, "Sending Boxcar notification");

  // Initialize cURL
  curl_global_init(CURL_GLOBAL_ALL);

  // Create a cURL easy session
  curl = curl_easy_init();
  if (curl == NULL) {
    syslog(LOG_ERR, "Failed to create cURL session");
    return -1;
  }

  // Enable verbose output
  curl_easy_setopt(curl, CURLOPT_VERBOSE, 1L);

  // Disable SSL peer/host verification
  curl_easy_setopt(curl, CURLOPT_SSL_VERIFYPEER, 0L);
  curl_easy_setopt(curl, CURLOPT_SSL_VERIFYHOST, 0L);

  // Set the URL
  curl_easy_setopt(curl, CURLOPT_URL, URL);

  // Set the POST data
  snprintf(str, sizeof(str),
    "user_credentials=%s&"
    "notification[sound]=%s&"
    "notification[title]=%s&"
    "notification[long_message]=%s",
    boxcar->access_token, boxcar->sound, title, message);
  curl_easy_setopt(curl, CURLOPT_POSTFIELDS, str);

  // Perform the request
  res = curl_easy_perform(curl);
  if (res != CURLE_OK) {
    syslog(LOG_ERR, "Failed to perform POST: %s", curl_easy_strerror(res));
    return -1;
  }

  // Cleanup
  curl_easy_cleanup(curl);
  curl_global_cleanup();

  return 0;
}

