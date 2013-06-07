/*
 * Copyright (C) 2013, Jason Rush
 */
#ifndef __LIST_H__
#define __LIST_H__

typedef struct list_item_t {
  void *data;
  struct list_item_t *prev;
  struct list_item_t *next;
} list_item_t;

typedef struct {
  list_item_t *head;
  list_item_t *tail;
} list_t;

void list_init(list_t *list);
void list_release(list_t *list);
void list_release_data(list_t *list);

int list_length(list_t *list);

list_item_t* list_last(list_t *list);
list_item_t* list_first(list_t *list);

void list_append(list_t *list, void *data);
void list_prepend(list_t *list, void *data);

void list_remove_item(list_t *list, list_item_t *item);
void list_remove(list_t *list, void *data);

#endif
