/*
 * Copyright (C) 2013, Jason Rush
 */
#include "action_boxcar.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <syslog.h>
#include <curl/curl.h>

#define URL "https://new.boxcar.io/api/notifications"

static int action_boxcar_callback(void *action_ptr, int value);

action_boxcar_t* action_boxcar_alloc(config_t *config, const char *prefix) {
  action_boxcar_t *action;
  char str[1024];

  // Allocate the structure
  action = (action_boxcar_t*)malloc(sizeof(action_boxcar_t));
  if (action == NULL) {
    syslog(LOG_ERR, "Failed to allocate structure");
    return NULL;
  }

  // Initialize the structure
  action->parent.callback = action_boxcar_callback;

  // Get the access token
  snprintf(str, sizeof(str), "%s.access_token", prefix);
  const char *access_token = config_get_string(config, str, NULL);
  if (access_token == NULL) {
    syslog(LOG_ERR, "Missing required parameter %s", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->access_token, access_token, sizeof(action->access_token));

  // Get the sound
  snprintf(str, sizeof(str), "%s.sound", prefix);
  const char *sound = config_get_string(config, str, NULL);
  if (sound == NULL) {
    syslog(LOG_ERR, "Missing required parameter %s", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->sound, sound, sizeof(action->sound));

  // Get the title
  snprintf(str, sizeof(str), "%s.title", prefix);
  const char *title = config_get_string(config, str, NULL);
  if (title == NULL) {
    syslog(LOG_ERR, "Missing required parameter %s", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->title, title, sizeof(action->title));

  // Get the message
  snprintf(str, sizeof(str), "%s.message", prefix);
  const char *message = config_get_string(config, str, NULL);
  if (message == NULL) {
    syslog(LOG_ERR, "Missing required parameter %s", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->message, message, sizeof(action->message));

  return action;
}

static int action_boxcar_callback(void *action_ptr, int value) {
  action_boxcar_t *action = (action_boxcar_t*)action_ptr;
  CURL *curl;
  CURLcode res;
  char str[1024];

  if (value == 0) {
    return 0;
  }

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
    "user_credentials=%s&notification[title]=%s&"
    "notification[long_message]=%s&notification[sound]=%s",
    action->access_token, action->title, action->message, action->sound);
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

