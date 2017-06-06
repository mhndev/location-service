#!/bin/bash

function mkfileP()
{
    mkdir -p "$(dirname "$1")" || return; touch "$1";
}


# Don't let root in:

#if [ $(whoami) != 'www-data' ]; then
#    printf "\x1b[31m >  Please run as www-data , your current user is $(whoami) \x1b[0m \n"
#    exit 1
#fi


printf "\033[0;32m > Bootup ...\x1b[0m \n"


if [ -f $HOME/docker/initialied ]; then
    rm $HOME/docker/initialized
fi

## install composer
printf "\033[0;32m > Installing Composer Packages ...\x1b[0m \n"

ls /var/log

if [ ! -d /var/log/apache2/ ]; then
   printf "\033[0;32m > Creating apache log directory cause it does not exist ... \x1b[0m \n"
   mkdir /var/log/apache2
fi


chown -R www-data:www-data /var/www

cd /var/www/

su -s /bin/bash -c "composer install --ignore-platform-reqs & > /dev/null 2>&1" www-data

printf "\033[0;32m > Feed Initial location data ...\x1b[0m \n"
feed > /dev/null 2>&1

printf "\033[0;32m > Sync Environment and Configuration Files ...\x1b[0m \n"
cp /docker/app/.env /var/www/html/

mkfileP $HOME/docker/initialized
