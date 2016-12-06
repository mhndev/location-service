#!/bin/bash

## Work Dir is /var/www
#cd /var/www/
arg=$1
if [ $arg = "init"  ]; then
    rm /docker/initialized
fi

if [ ! -f /docker/initialized ]; then
   composer install --ignore-platform-reqs --no-dev
   cp /docker/app/.env ./

   touch /docker/initialized
fi

## continue with default Parent CMD
( exec "/run.sh" )
