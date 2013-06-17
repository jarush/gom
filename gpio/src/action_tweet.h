/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __ACTION_TWEET_H__
#define __ACTION_TWEET_H__

#include "action.h"

typedef struct {
  action_t parent;
  char username[128];
  char password[128];
  char message[256];
} action_tweet_t;

action_tweet_t* action_tweet_alloc(config_t *config, const char *prefix);

#endif
