/*
 * Copyright (C) 2014, Jason Rush
 */
#include "command_process.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <syslog.h>

#include "str_utils.h"
#include "doors.h"

#define MAX_TOKENS 16

void command_get(netcon_client_t *netcon_client, char *tokens[], int num_tokens);
void command_set(netcon_client_t *netcon_client, char *tokens[], int num_tokens);
void command_toggle(netcon_client_t *netcon_client, char *tokens[], int num_tokens);
void command_exit(netcon_client_t *netcon_client, char *tokens[], int num_tokens);

int command_process(netcon_client_t *netcon_client, const char *command) {
  char *tokens[MAX_TOKENS];
  char str[1024];
  int num_tokens;

  syslog(LOG_DEBUG, "Command: %s", command);

  // Make a copy of the command since we modify it
  strncpy(str, command, sizeof(str));

  num_tokens = str_split(str, tokens, MAX_TOKENS, ' ');
  if (num_tokens == 0) {
    return 0;
  }

  if (strcmp(tokens[0], "get") == 0) {
    command_get(netcon_client, tokens, num_tokens);
  } else if (strcmp(tokens[0], "toggle") == 0) {
    command_toggle(netcon_client, tokens, num_tokens);
  } else if (strcmp(tokens[0], "exit") == 0) {
    command_exit(netcon_client, tokens, num_tokens);
  } else {
    syslog(LOG_WARNING, "Invalid command: %s", command);
  }

  return 0;
}

void command_get(netcon_client_t *netcon_client, char *tokens[], int num_tokens) {
  char str[1024];

  if (num_tokens != 2) {
    syslog(LOG_WARNING, "Invalid number of parameters");
    netcon_client_send(netcon_client, "ERROR Invalid number of parameters\n");
    return;
  }

  int idx = atoi(tokens[1]);

  // Get the door
  door_t *door = doors_get_door(idx);
  if (door == NULL) {
    syslog(LOG_WARNING, "No such door: %d", idx);
    netcon_client_send(netcon_client, "ERROR No such door\n");
    return;
  }

  // Read the current status
  int value = door_get_status(door);

  snprintf(str, sizeof(str), "%d\n", value);
  netcon_client_send(netcon_client, str);
}

void command_toggle(netcon_client_t *netcon_client, char *tokens[], int num_tokens) {
  if (num_tokens != 2) {
    syslog(LOG_WARNING, "Invalid number of parameters");
    netcon_client_send(netcon_client, "ERROR Invalid number of parameters\n");
    return;
  }

  int idx = atoi(tokens[1]);

  // Get the door
  door_t *door = doors_get_door(idx);
  if (door == NULL) {
    syslog(LOG_WARNING, "No such door: %d", idx);
    netcon_client_send(netcon_client, "ERROR No such door\n");
    return;
  }

  // Toggle the door replay
  door_toggle(door);

  netcon_client_send(netcon_client, "OK\n");
}

void command_exit(netcon_client_t *netcon_client, char *tokens[], int num_tokens) {
  netcon_client_send(netcon_client, "OK\n");

  syslog(LOG_INFO, "Performing remote exit");

  exit(0);
}

