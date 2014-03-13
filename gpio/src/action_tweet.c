/*
 * Copyright (C) 2013, Jason Rush
 */
#include "action_tweet.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <syslog.h>
#include <curl/curl.h>

#define URL "http://api.supertweet.net/1.1/statuses/update.json"

static int action_tweet_callback(void *action_ptr, int value);
static size_t write_callback(char *ptr, size_t size, size_t nmemb, void *userdata);

action_tweet_t* action_tweet_alloc(config_t *config, const char *prefix) {
  action_tweet_t *action;
  char str[1024];

  // Allocate the structure
  action = (action_tweet_t*)malloc(sizeof(action_tweet_t));
  if (action == NULL) {
    syslog(LOG_ERR, "Failed to allocate structure");
    return NULL;
  }

  // Initialize the structure
  action->parent.callback = action_tweet_callback;

  // Get the username
  snprintf(str, sizeof(str), "%s.username", prefix);
  const char *username = config_get_string(config, str, NULL);
  if (username == NULL) {
    syslog(LOG_ERR, "Missing required parameter %s", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->username, username, sizeof(action->username));

  // Get the password
  snprintf(str, sizeof(str), "%s.password", prefix);
  const char *password = config_get_string(config, str, NULL);
  if (password == NULL) {
    syslog(LOG_ERR, "Missing required parameter %s", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->password, password, sizeof(action->password));

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

static int action_tweet_callback(void *action_ptr, int value) {
  action_tweet_t *action = (action_tweet_t*)action_ptr;
  CURL *curl;
  CURLcode res;
  char str[1024];

  if (value == 0) {
    return 0;
  }

  syslog(LOG_INFO, "Tweeting");

  // Initialize cURL
  curl_global_init(CURL_GLOBAL_ALL);

  // Create a cURL easy session
  curl = curl_easy_init();
  if (curl == NULL) {
    syslog(LOG_ERR, "Failed to create cURL session");
    return -1;
  }

  // Set a function to handle the response
  curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, write_callback);

  // Set the URL
  curl_easy_setopt(curl, CURLOPT_URL, URL);

  // Set the username/password
  curl_easy_setopt(curl, CURLOPT_USERNAME, action->username);
  curl_easy_setopt(curl, CURLOPT_PASSWORD, action->password);

  // Set the POST data
  snprintf(str, sizeof(str), "status=%s", action->message);
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

size_t write_callback(char *ptr, size_t size, size_t nmemb, void *userdata) {
  return size * nmemb;
}

