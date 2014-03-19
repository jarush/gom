#!/bin/sh

TARGETDIR=$1

rm -f $TARGETDIR/etc/os-release
rm -f $TARGETDIR/etc/init.d/S20urandom

cp $RPI_PATH/gomd/src/gomd.properties $TARGETDIR/etc/
cp $RPI_PATH/gomd/src/gomd.properties $TARGETDIR/config/
cp $RPI_PATH/gomd/src/gomd $TARGETDIR/usr/bin/

cp -R $RPI_PATH/web/* $TARGETDIR/var/www/

