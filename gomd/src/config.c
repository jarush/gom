/*
 * Copyright (C) 2014, Jason Rush
 */
#include "config.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <assert.h>
#include "str_utils.h"

#define MAX_LINE_LENGTH 1024
#define MAX_VALUE_LENGTH 128

static option_t* config_get_option(config_t *config, const char *name);

void config_init(config_t *config) {
  memset(config, 0, sizeof(config_t));
}

void config_release(config_t *config) {
  option_t *option = config->options;
  option_t *tmp;

  // Loop over the options
  while (option != NULL) {
    // Free the name and value string
    free(option->name);
    free(option->value);

    // Free the option
    tmp = option->next;
    free(option);
    option = tmp;
  }

  config->options = NULL;
}

int config_read(config_t *config, const char *filename) {
  FILE *fp;
  char line_buffer[MAX_LINE_LENGTH];
  char *line;
  int line_number = 0;
  option_t *option;
  char *ptr;
  char *name;
  char *value;

  // Open the file
  if ((fp = fopen(filename, "r")) == NULL) {
    fprintf(stderr, "Failed to open file: %s\n", filename);
    return -1;
  }

  // Read lines of the file
  while ((line = fgets(line_buffer, sizeof(line_buffer), fp)) != NULL) {
    line_number++;

    // Strip off any comments
    ptr = strchr(line, '#');
    if (ptr != NULL) {
      *ptr = '\0';
    }

    // Trim off excess whitespace
    line = str_trim(line);

    // Skip blank lines
    if (line[0] == '\0') {
      continue;
    }

    // Find the separator
    ptr = strchr(line, '=');
    if (ptr == NULL) {
      fprintf(stderr, "Syntax error on line %d: Missing separator character\n", line_number);
      fclose(fp);
      return -1;
    }

    // Replace the separator with a NULL
    *ptr = '\0';

    // Assign the name and value pointers
    name = line;
    value = ptr + 1;

    // Trim off excess white space
    name = str_trim(name);
    value = str_trim(value);

    // Make sure the name string has contents
    if (name[0] == '\0') {
      fprintf(stderr, "Syntax error on line %d: No name provided\n", line_number);
      fclose(fp);
      return -1;
    }

    // Check if the option already exists
    option = config_get_option(config, name);
    if (option != NULL) {
      fprintf(stderr, "Option already exists on line %d\n", line_number);
      fclose(fp);
      return -1;
    }

    // Allocate a new option
    option = malloc(sizeof(option_t));
    assert(option != NULL);

    // Duplicate the name string
    option->name = strdup(name);
    assert(option->name != NULL);

    // Duplicate the value string
    option->value = strdup(value);
    assert(option->value != NULL);

    // Update the linked list pointers
    option->next = config->options;
    config->options = option;
  }

  fclose(fp);

  return 0;
}

int config_write(config_t *config, const char *filename) {
  FILE *fp;
  option_t *option;

  // Open the file
  if ((fp = fopen(filename, "w")) == NULL) {
    fprintf(stderr, "Failed to open file: %s\n", filename);
    return -1;
  }

  // Loop over the options
  for (option = config->options; option != NULL; option = option->next) {
    // Print out the option
    fprintf(fp, "%s=%s\n", option->name, option->value);
  }

  fclose(fp);

  return 0;
}

option_t* config_get_option(config_t *config, const char *name) {
  option_t *option;

  // Loop over the options
  for (option = config->options; option != NULL; option = option->next) {
    // Check if this option's name matches
    if (strcmp(option->name, name) == 0) {
      return option;
    }
  }

  return NULL;
}

const char* config_get_string(config_t *config,
    const char *name,
    const char *default_value) {
  const option_t *option = config_get_option(config, name);
  if (option == NULL) {
    return default_value;
  }

  return option->value;
}

int32_t config_get_int32(config_t *config,
    const char *name,
    int32_t default_value) {
  const option_t *option = config_get_option(config, name);
  char *endptr;
  int32_t value;

  if (option == NULL) {
    return default_value;
  }

  value = strtol(option->value, &endptr, 0);
  if (option->value == endptr) {
    value = default_value;
  }

  return value;
}

uint32_t config_get_uint32(config_t *config,
    const char *name,
    uint32_t default_value) {
  const option_t *option = config_get_option(config, name);
  char *endptr;
  uint32_t value;

  if (option == NULL) {
    return default_value;
  }

  value = strtoul(option->value, &endptr, 0);
  if (option->value == endptr) {
    value = default_value;
  }

  return value;
}


int64_t config_get_int64(config_t *config,
    const char *name,
    int64_t default_value) {
  const option_t *option = config_get_option(config, name);
  char *endptr;
  int64_t value;

  if (option == NULL) {
    return default_value;
  }

  value = strtoll(option->value, &endptr, 0);
  if (option->value == endptr) {
    value = default_value;
  }

  return value;
}

uint64_t config_get_uint64(config_t *config,
    const char *name,
    uint64_t default_value) {
  const option_t *option = config_get_option(config, name);
  char *endptr;
  uint64_t value;

  if (option == NULL) {
    return default_value;
  }

  value = strtoull(option->value, &endptr, 0);
  if (option->value == endptr) {
    value = default_value;
  }

  return value;
}

float config_get_float(config_t *config,
    const char *name,
    float default_value) {
  const option_t *option = config_get_option(config, name);
  char *endptr;
  float value;

  if (option == NULL) {
    return default_value;
  }

  value = (float)strtod(option->value, &endptr);
  if (option->value == endptr) {
    value = default_value;
  }

  return value;
}

double config_get_double(config_t *config,
    const char *name,
    double default_value) {
  char *endptr;
  double value;

  const option_t *option = config_get_option(config, name);
  if (option == NULL) {
    return default_value;
  }

  value = strtod(option->value, &endptr);
  if (option->value == endptr) {
    value = default_value;
  }

  return value;
}

int config_get_boolean(config_t *config,
    const char *name,
    int default_value) {
      const option_t *option = config_get_option(config, name);
  if (option == NULL) {
    return default_value;
  }

  if (strcasecmp(option->value, "true") == 0 ||
      strcasecmp(option->value, "yes") == 0 ||
      strcasecmp(option->value, "1") == 0) {
    return 1;
  }

  return 0;
}

void config_set_string(config_t *config,
    const char *name,
    const char *value) {
  option_t *option = config_get_option(config, name);
  if (option != NULL) {
    free(option->value);
  } else {
    option = malloc(sizeof(option_t));
    assert(option != NULL);

    option->name = strdup(name);

    option->next = config->options;
    config->options = option;
  }

  option->value = strdup(value);
  assert(option->value != NULL);
}

void config_set_int(config_t *config,
    const char *name,
    int value) {
  char str[MAX_VALUE_LENGTH];

  snprintf(str, sizeof(str), "%d", value);
  config_set_string(config, name, str);
}

void config_set_uint(config_t *config,
    const char *name,
    unsigned int value) {
  char str[MAX_VALUE_LENGTH];

  snprintf(str, sizeof(str), "%u", value);
  config_set_string(config, name, str);
}

void config_set_float(config_t *config,
    const char *name,
    float value) {
  char str[MAX_VALUE_LENGTH];

  snprintf(str, sizeof(str), "%f", value);
  config_set_string(config, name, str);
}

void config_set_double(config_t *config,
    const char *name,
    double value) {
  char str[MAX_VALUE_LENGTH];

  snprintf(str, sizeof(str), "%f", value);
  config_set_string(config, name, str);
}

void config_set_boolean(config_t *config,
    const char *name,
    int value) {
  config_set_string(config, name, value ? "true" : "false");
}
