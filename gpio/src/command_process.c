/*
 * Copyright (C) 2013, Jason Rush
 */
#include "command_process.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "str_utils.h"
#include "gpio_mgr.h"

#define MAX_TOKENS 16

void command_get(netcon_client_t *netcon_client, char *tokens[], int num_tokens);
void command_set(netcon_client_t *netcon_client, char *tokens[], int num_tokens);
void command_exit(netcon_client_t *netcon_client, char *tokens[], int num_tokens);

int command_process(netcon_client_t *netcon_client, const char *command) {
  char *tokens[MAX_TOKENS];
  char str[1024];
  int num_tokens;

  printf("Command: %s\n", command);

  // Make a copy of the command since we modify it
  strncpy(str, command, sizeof(str));

  num_tokens = str_split(str, tokens, MAX_TOKENS, ' ');
  if (num_tokens == 0) {
    return 0;
  }

  if (strcmp(tokens[0], "get") == 0) {
    command_get(netcon_client, tokens, num_tokens);
  } else if (strcmp(tokens[0], "get") == 0) {
    command_set(netcon_client, tokens, num_tokens);
  } else if (strcmp(tokens[0], "exit") == 0) {
    command_exit(netcon_client, tokens, num_tokens);
  } else {
    fprintf(stderr, "Invalid command: %s\n", command);
  }

  return 0;
}

void command_get(netcon_client_t *netcon_client, char *tokens[], int num_tokens) {
  char str[1024];

  if (num_tokens != 2) {
    fprintf(stderr, "Invalid number of parameters\n");
    return;
  }

  int idx = atoi(tokens[1]);

  // Get the gpio
  gpio_t *gpio = gpio_mgr_get(idx);
  if (gpio == NULL) {
    fprintf(stderr, "No such gpio: %d\n", idx);
    return;
  }

  // Read the current value
  int value = gpio_read(gpio);

  snprintf(str, sizeof(str), "%d\n", value);
  netcon_client_send(netcon_client, str);
}

void command_set(netcon_client_t *netcon_client, char *tokens[], int num_tokens) {
  if (num_tokens != 3) {
    fprintf(stderr, "Invalid number of parameters\n");
    return;
  }

  int idx = atoi(tokens[1]);
  int value = atoi(tokens[2]);

  // Get the gpio
  gpio_t *gpio = gpio_mgr_get(idx);
  if (gpio == NULL) {
    fprintf(stderr, "No such gpio: %d\n", idx);
    return;
  }

  // Set or clear the gpio
  if (value) {
    gpio_set(gpio);
  } else {
    gpio_clr(gpio);
  }

  netcon_client_send(netcon_client, "OK\n");
}

void command_exit(netcon_client_t *netcon_client, char *tokens[], int num_tokens) {
  netcon_client_send(netcon_client, "OK\n");

  fprintf(stderr, "Performing remote exit\n");

  exit(0);
}

