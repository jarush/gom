/*
 * Copyright (C) 2014, Jason Rush
 */
#ifndef __CONFIG_H__
#define __CONFIG_H__

#include <stdint.h>

typedef struct option_t {
  char *name;
  char *value;
  struct option_t *next;
} option_t;

typedef struct {
  option_t *options;
} config_t;

void config_init(config_t *config);
void config_release(config_t *config);

int config_read(config_t *config, const char *filename);
int config_write(config_t *config, const char *filename);

const char* config_get_string(config_t *config,
    const char *name,
    const char *default_value);
int32_t config_get_int32(config_t *config,
    const char *name,
    int32_t default_value);
uint32_t config_get_uint32(config_t *config,
    const char *name,
    uint32_t default_value);
int64_t config_get_int64(config_t *config,
    const char *name,
    int64_t default_value);
uint64_t config_get_uint64(config_t *config,
    const char *name,
    uint64_t default_value);
float config_get_float(config_t *config,
    const char *name,
    float default_value);
double config_get_double(config_t *config,
    const char *name,
    double default_value);
int config_get_boolean(config_t *config,
    const char *name,
    int default_value);

void config_set_string(config_t *config,
    const char *name,
    const char *value);
void config_set_int(config_t *config,
    const char *name,
    int value);
void config_set_uint(config_t *config,
    const char *name,
    unsigned int value);
void config_set_float(config_t *config,
    const char *name,
    float value);
void config_set_double(config_t *config,
    const char *name,
    double value);
void config_set_boolean(config_t *config,
    const char *name,
    int value);

#endif
