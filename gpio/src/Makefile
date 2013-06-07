CC = arm-linux-gnueabihf-gcc
CPPFLAGS = -I../bcm2835-1.25/src/
CFLAGS = -g -Wall
LIBS = ../bcm2835-1.25/src/libbcm2835.a

TARGET = gpiod
SRCS = $(wildcard *.c)
OBJS = $(SRCS:.c=.o)

$(TARGET): $(OBJS)
	$(CC) $(LDFLAGS) -o $@ $^ $(LIBS)

.PHONEY: clean
clean:
	rm -f $(TARGET) $(OBJS)

