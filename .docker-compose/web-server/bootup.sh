#!/bin/bash
printf "\033[0;32m > Bootup ...\n"

rm /docker/initialized

## install composer
printf "\033[0;32m > Installing Composer Packages ...\n"
(cd /var/www/html/ && composer install --ignore-platform-reqs)

printf "\033[0;32m > Sync Environment and Configuration Files ...\n"
cp /docker/app/.env /var/www/html/
chmod 0777 -R /data/logs/

touch /docker/initialized