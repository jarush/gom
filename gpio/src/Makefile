SYSROOT=../../buildroot-2014.02/output/host/usr/arm-buildroot-linux-gnueabihf/sysroot
CC = arm-linux-gnueabihf-gcc
CPPFLAGS = -I../bcm2835-1.25/src/ -I$(SYSROOT)/usr/include
CFLAGS = -g -Wall
LDFLAGS = -L$(SYSROOT)/usr/lib \
          -Wl,-rpath-link=$(SYSROOT)/usr/lib
LIBS = ../bcm2835-1.25/src/libbcm2835.a -lcurl -lrt

TARGET = gpiod
SRCS = $(wildcard *.c)
OBJS = $(SRCS:.c=.o)

$(TARGET): $(OBJS)
	$(CC) $(LDFLAGS) -o $@ $^ $(LIBS)

.PHONEY: clean
clean:
	rm -f $(TARGET) $(OBJS)

