/*
 * Copyright (C) 2014, Jason Rush
 */
#include <string.h>
#include <ctype.h>

#include "str_utils.h"

void str_chomp(char *str) {
  int i = strlen(str) - 1;

  while (i >= 0 && (str[i] == '\n' || str[i] == '\r')) {
    str[i--] = '\0';
  }
}

char str_chop(char *str) {
  int i = strlen(str) - 1;
  char c = 0;

  if (i >= 0) {
    c = str[i];
    str[i--] = '\0';
  }

  return c;
}

char* str_ltrim(char *str) {
  char *ptr = str;

  /* Trim whitespace from the left side of the string */
  while (*ptr != '\0' && isspace(*ptr)) {
    ptr++;
  }

  return ptr;
}

char* str_rtrim(char *str) {
  char *ptr = str + strlen(str) - 1;

  /* Trim  whitespacei from the right side of the string */
  while (ptr >= str && isspace(*ptr)) {
    ptr--;
  }

  *(ptr + 1) = '\0';

  return str;
}

char* str_trim(char *str) {
  char *ptr = str;

  ptr = str_ltrim(ptr);
  ptr = str_rtrim(ptr);

  return ptr;
}

int str_split(char *str, char *fields[], int nfields, char sep) {
  char *ptr = str;
  char **fp = fields;
  int fn = nfields;

  if (*ptr == '\0') {
    return 0;
  }

  while (1) {
    *fp++ = ptr;
    if (--fn == 0) {
      break;
    }

    // Look for the first separators
    while (*ptr != sep) {
      if (*ptr == '\0') {
        return nfields - fn;
      }
      ptr++;
    }

    // Skip over all the separator characters
    while (*ptr == sep) {
      *ptr++ = '\0';
    }
  }

  return nfields - fn;
}
