#!/bin/bash

## Work Dir is /var/www
#cd /var/www/
if [ ! -f /docker/initialized ]; then
   init
fi

## continue with default Parent CMD
( exec "/run.sh" )
