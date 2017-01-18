#!/bin/bash
printf "\033[0;32m > Feed ...\n"

if [ ! -f /docker/initialized ]; then
   bootup
fi

printf "\033[0;32m > Feed Location Indexes Into Elastic ...\n"
php /var/www/index_elastic.php
