/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __GPIO_MGR_H__
#define __GPIO_MGR_H__

#include "gpio.h"
#include "config.h"

#define MAX_GPIO 32

int gpio_mgr_init(config_t *config);
void gpio_mgr_release(void);
int gpio_mgr_load_config(config_t *config);
int gpio_mgr_add(gpio_t *gpio);
gpio_t* gpio_mgr_get(int index);

#endif


