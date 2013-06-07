/*
 * Copyright (C) 2013, Jason Rush
 */
#include "list.h"

#include <stdio.h>
#include <stdlib.h>
#include <assert.h>

void list_init(list_t *list) {
  // Initialize the head/tail pointers
  list->head = NULL;
  list->tail = NULL;
}

void list_release(list_t *list) {
  list_item_t *item = list->head;
  list_item_t *tmp;

  while (item != NULL) {
    tmp = item->next;
    free(item);
    item = tmp;
  }
}

void list_release_data(list_t *list) {
  list_item_t *item = list->head;
  list_item_t *tmp;

  while (item != NULL) {
    tmp = item->next;
    if (item->data != NULL) {
      free(item->data);
    }
    free(item);
    item = tmp;
  }
}

int list_length(list_t *list) {
  list_item_t *item;
  int n = 0;

  for (item = list_first(list); item != NULL; item = item->next) {
    n++;
  }

  return n;
}

list_item_t* list_first(list_t *list) {
  return list->head;
}

list_item_t* list_last(list_t *list) {
  return list->tail;
}

void list_append(list_t *list, void *data) {
  list_item_t *item;

  // Allocate the item
  item = malloc(sizeof(list_item_t));
  assert(item != NULL);

  // Initialize the item
  item->data = data;
  item->prev = list->tail;
  item->next = NULL;

  // Set the old tail's next pointer to the new item
  if (list->tail != NULL) {
    list->tail->next = item;
  }

  // Set the tail
  list->tail = item;

  // Set the head if it's the first item
  if (list->head == NULL) {
    list->head = item;
  }
}

void list_prepend(list_t *list, void *data) {
  list_item_t *item;

  // Allocate the item
  item = malloc(sizeof(list_t));
  assert(item != NULL);

  // Initialize the item
  item->data = data;
  item->prev = NULL;
  item->next = list->head;

  // Set the old head's next pointer to the new item
  if (list->head != NULL) {
    list->head->prev = item;
  }

  // Set the head
  list->head = item;

  // Set the tail if it's the first item
  if (list->tail == NULL) {
    list->tail = item;
  }
}

void list_remove_item(list_t *list, list_item_t *item) {
  // Update the previous and next pointers of the surrounding items
  if (item->prev != NULL) {
    item->prev->next = item->next;
  }
  if (item->next != NULL) {
    item->next->prev = item->prev;
  }

  // Update the head and tail if this item was either
  if (list->head == item) {
    list->head = item->next;
  }
  if (list->tail == item) {
    list->tail = item->prev;
  }

  // Delete the item
  free(item);
}

void list_remove(list_t *list, void *data) {
  list_item_t *item;

  // Find the item
  for (item = list_first(list); item != NULL; item = item->next) {
    if (item->data == data) {
      list_remove_item(list, item);
      break;
    }
  }
}
