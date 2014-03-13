/*
 * Copyright (C) 2013, Jason Rush
 */
#include "action_email.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <curl/curl.h>

typedef struct {
  char message[1024];
  char msg_len;
  char *msg_ptr;
} email_msg_t;

static int action_email_callback(void *action_ptr, int value);
static size_t read_callback(void *ptr, size_t size, size_t nmemb, void *userdata);

action_email_t* action_email_alloc(config_t *config, const char *prefix) {
  action_email_t *action;
  char str[1024];

  // Allocate the structure
  action = (action_email_t*)malloc(sizeof(action_email_t));
  if (action == NULL) {
    fprintf(stderr, "Failed to allocate structure\n");
    return NULL;
  }

  // Initialize the structure
  action->parent.callback = action_email_callback;

  // Get the url
  snprintf(str, sizeof(str), "%s.url", prefix);
  const char *url = config_get_string(config, str, NULL);
  if (url == NULL) {
    fprintf(stderr, "Missing required parameter %s\n", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->url, url, sizeof(action->url));

  // Get the from address
  snprintf(str, sizeof(str), "%s.from", prefix);
  const char *from = config_get_string(config, str, NULL);
  if (from == NULL) {
    fprintf(stderr, "Missing required parameter %s\n", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->from, from, sizeof(action->from));

  // Get the to address
  snprintf(str, sizeof(str), "%s.to", prefix);
  const char *to = config_get_string(config, str, NULL);
  if (to == NULL) {
    fprintf(stderr, "Missing required parameter %s\n", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->to, to, sizeof(action->to));

  // Get the username
  snprintf(str, sizeof(str), "%s.username", prefix);
  const char *username = config_get_string(config, str, NULL);
  if (username == NULL) {
    fprintf(stderr, "Missing required parameter %s\n", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->username, username, sizeof(action->username));

  // Get the password
  snprintf(str, sizeof(str), "%s.password", prefix);
  const char *password = config_get_string(config, str, NULL);
  if (password == NULL) {
    fprintf(stderr, "Missing required parameter %s\n", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->password, password, sizeof(action->password));

  // Get the subject
  snprintf(str, sizeof(str), "%s.subject", prefix);
  const char *subject = config_get_string(config, str, NULL);
  if (subject == NULL) {
    fprintf(stderr, "Missing required parameter %s\n", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->subject, subject, sizeof(action->subject));

  // Get the message
  snprintf(str, sizeof(str), "%s.message", prefix);
  const char *message = config_get_string(config, str, NULL);
  if (message == NULL) {
    fprintf(stderr, "Missing required parameter %s\n", str);
    action_release((action_t*)action);
    return NULL;
  }
  strncpy(action->message, message, sizeof(action->message));

  return action;
}

static int action_email_callback(void *action_ptr, int value) {
  action_email_t *action = (action_email_t*)action_ptr;
  CURL *curl;
  struct curl_slist *recipients = NULL;
  CURLcode res;

  if (value == 0) {
    return 0;
  }

  printf("Emailing\n");

  // Initialize cURL
  curl_global_init(CURL_GLOBAL_ALL);

  // Create a cURL easy session
  curl = curl_easy_init();
  if (curl == NULL) {
    fprintf(stderr, "Failed to create cURL session\n");
    return -1;
  }

  // Set a function to handle the response
  curl_easy_setopt(curl, CURLOPT_VERBOSE, 1L);

  // Set the URL
  curl_easy_setopt(curl, CURLOPT_URL, action->url);

  // Set option to use SSL
  curl_easy_setopt(curl, CURLOPT_USE_SSL, (long)CURLUSESSL_ALL);
  curl_easy_setopt(curl, CURLOPT_SSL_VERIFYPEER, 0L);
  curl_easy_setopt(curl, CURLOPT_SSL_VERIFYHOST, 0L);

  // Set the username/password
  curl_easy_setopt(curl, CURLOPT_USERNAME, action->username);
  curl_easy_setopt(curl, CURLOPT_PASSWORD, action->password);

  // Set the from address
  curl_easy_setopt(curl, CURLOPT_MAIL_FROM, action->from);

  // Set the to address
  recipients = curl_slist_append(recipients, action->to);
  curl_easy_setopt(curl, CURLOPT_MAIL_RCPT, recipients);

  // Create the email message

  email_msg_t email_msg;
  email_msg.msg_len = snprintf(email_msg.message, sizeof(email_msg.message),
      "Subject: %s\r\n\r\n%s", action->subject, action->message);
  email_msg.msg_ptr = email_msg.message;

  // Set the function to send the email body
  curl_easy_setopt(curl, CURLOPT_READFUNCTION, read_callback);
  curl_easy_setopt(curl, CURLOPT_READDATA, &email_msg);
  curl_easy_setopt(curl, CURLOPT_UPLOAD, 1L);

  // Perform the request
  res = curl_easy_perform(curl);
  if (res != CURLE_OK) {
    fprintf(stderr, "Failed to send email: %s\n", curl_easy_strerror(res));
    return -1;
  }

  // Cleanup
  curl_slist_free_all(recipients);
  curl_easy_cleanup(curl);
  curl_global_cleanup();

  return 0;
}

static size_t read_callback(void *ptr, size_t size, size_t nmemb, void *userdata) {
  email_msg_t *email_msg = (email_msg_t*)userdata;

  size_t n = size * nmemb;
  if (n < 1) {
    return 0;
  }

  if (n > email_msg->msg_len) {
    n = email_msg->msg_len;
  }

  memcpy(ptr, email_msg->msg_ptr, n);

  email_msg->msg_ptr += n;
  email_msg->msg_len -= n;

  return n;
}

