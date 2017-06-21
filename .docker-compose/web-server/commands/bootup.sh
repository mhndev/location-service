#!/bin/bash

# Run as www-data
if [ `whoami` != 'www-data' ]; then
        echo "Please run as www-data"
        exit 1
fi

printf "\033[0;32m > composer install ...\n"
(cd /var/www/ && composer install --no-dev)


touch /docker/initialized

excelToJson > /dev/null 2>&1

feed > /dev/null 2>&1

printf "\033[0;32m > Sync Environment and Configuration Files ...\x1b[0m \n"
cp /docker/app/.env /var/www/html/
