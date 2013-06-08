/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __GPIO_H__
#define __GPIO_H__

#include "action.h"
#include "event_mgr.h"

#define GPIO_DIR_INPUT  0
#define GPIO_DIR_OUTPUT 1

typedef struct {
  int pin;
  int direction;
  int active_low;
  action_t *action;
  int fd;
  int previous_value;
} gpio_t;

gpio_t* gpio_alloc(int pin);
int gpio_set_input(gpio_t *gpio, int sec, int nsec,
    int active_low, action_t *action);
int gpio_set_output(gpio_t *gpio);
void gpio_release(gpio_t *gpio);

int gpio_read(gpio_t *gpio);

void gpio_set(gpio_t *gpio);
void gpio_clr(gpio_t *gpio);
void gpio_toggle_high(gpio_t *gpio, int duration);
void gpio_toggle_low(gpio_t *gpio, int duration);

int gpio_process(event_mgr_t *event_mgr, int fd, void *data);

#endif
