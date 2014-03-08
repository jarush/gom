#!/bin/bash

if [ -n "$RPI_PATH" ]; then
  echo "Environment already setup (RPI_PATH=$RPI_PATH)"
else
  export RPI_PATH=`pwd`
  echo RPI_PATH=$RPI_PATH

  if [ -d $RPI_PATH/tools/arm-bcm2708/gcc-linaro-arm-linux-gnueabihf-raspbian/bin ]; then
    echo "Adding toolchain to PATH"
    export PATH=$PATH:$RPI_PATH/tools/arm-bcm2708/gcc-linaro-arm-linux-gnueabihf-raspbian/bin
  fi
fi

