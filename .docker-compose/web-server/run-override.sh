#!/bin/bash

## Work Dir is /var/www
#cd /var/www/
if [ ! -f /docker/initialized ]; then
   bootup
fi

## continue with default Parent CMD
( exec "/run.sh" )
