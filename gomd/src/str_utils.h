/*
 * Copyright (C) 2014, Jason Rush
 */
#ifndef __STR_UTILS_H__
#define __STR_UTILS_H__

void str_chomp(char *str);
char str_chop(char *str);
char* str_ltrim(char *str);
char* str_rtrim(char *str);
char* str_trim(char *str);
int str_split(char *str, char *fields[], int nfields, char sep);

#endif
