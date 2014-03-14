/*
 * Copyright (C) 2014, Jason Rush
 */
#include "email.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <syslog.h>
#include <curl/curl.h>

typedef struct {
  char message[1024];
  char msg_len;
  char *msg_ptr;
} email_msg_t;

static size_t read_callback(void *ptr, size_t size,
  size_t nmemb, void *userdata);

email_t* email_alloc(config_t *config) {
  email_t *email;

  // Allocate the structure
  email = (email_t*)malloc(sizeof(email_t));
  if (email == NULL) {
    syslog(LOG_ERR, "Failed to allocate structure");
    return NULL;
  }

  // Get the url
  const char *url = config_get_string(config, "email.url", NULL);
  if (url == NULL) {
    syslog(LOG_ERR, "Missing required parameter 'email.url'");
    email_release(email);
    return NULL;
  }
  strncpy(email->url, url, sizeof(email->url));

  // Get the from address
  const char *from = config_get_string(config, "email.from", NULL);
  if (from == NULL) {
    syslog(LOG_ERR, "Missing required parameter 'email.from'");
    email_release(email);
    return NULL;
  }
  strncpy(email->from, from, sizeof(email->from));

  // Get the to address
  const char *to = config_get_string(config, "email.to", NULL);
  if (to == NULL) {
    syslog(LOG_ERR, "Missing required parameter 'email.to'");
    email_release(email);
    return NULL;
  }
  strncpy(email->to, to, sizeof(email->to));

  // Get the username
  const char *username = config_get_string(config, "email.username", NULL);
  if (username == NULL) {
    syslog(LOG_ERR, "Missing required parameter 'email.username'");
    email_release(email);
    return NULL;
  }
  strncpy(email->username, username, sizeof(email->username));

  // Get the password
  const char *password = config_get_string(config, "email.password", NULL);
  if (password == NULL) {
    syslog(LOG_ERR, "Missing required parameter 'email.password'");
    email_release(email);
    return NULL;
  }
  strncpy(email->password, password, sizeof(email->password));

  return email;
}

void email_release(email_t *email) {
  free(email);
}

int email_notify(email_t *email, const char *title, const char *message) {
  CURL *curl;
  struct curl_slist *recipients = NULL;
  CURLcode res;

  syslog(LOG_INFO, "Sending email notification");

  // Initialize cURL
  curl_global_init(CURL_GLOBAL_ALL);

  // Create a cURL easy session
  curl = curl_easy_init();
  if (curl == NULL) {
    syslog(LOG_ERR, "Failed to create cURL session");
    return -1;
  }

  // Set a function to handle the response
  curl_easy_setopt(curl, CURLOPT_VERBOSE, 1L);

  // Set the URL
  curl_easy_setopt(curl, CURLOPT_URL, email->url);

  // Set option to use SSL
  curl_easy_setopt(curl, CURLOPT_USE_SSL, (long)CURLUSESSL_ALL);
  curl_easy_setopt(curl, CURLOPT_SSL_VERIFYPEER, 0L);
  curl_easy_setopt(curl, CURLOPT_SSL_VERIFYHOST, 0L);

  // Set the username/password
  curl_easy_setopt(curl, CURLOPT_USERNAME, email->username);
  curl_easy_setopt(curl, CURLOPT_PASSWORD, email->password);

  // Set the from address
  curl_easy_setopt(curl, CURLOPT_MAIL_FROM, email->from);

  // Set the to address
  recipients = curl_slist_append(recipients, email->to);
  curl_easy_setopt(curl, CURLOPT_MAIL_RCPT, recipients);

  // Create the email message

  email_msg_t email_msg;
  email_msg.msg_len = snprintf(email_msg.message, sizeof(email_msg.message),
      "Subject: %s\r\n\r\n%s", title, message);
  email_msg.msg_ptr = email_msg.message;

  // Set the function to send the email body
  curl_easy_setopt(curl, CURLOPT_READFUNCTION, read_callback);
  curl_easy_setopt(curl, CURLOPT_READDATA, &email_msg);
  curl_easy_setopt(curl, CURLOPT_UPLOAD, 1L);

  // Perform the request
  res = curl_easy_perform(curl);
  if (res != CURLE_OK) {
    syslog(LOG_ERR, "Failed to send email: %s", curl_easy_strerror(res));
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

