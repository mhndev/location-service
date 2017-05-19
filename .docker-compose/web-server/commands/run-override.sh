#!/bin/bash

set -e



# Using this UID / GID allows local and live file modification of web site

usermod -u ${USER_ID} ${APACHE_RUN_USER} > /dev/null 2>&1
groupmod -g ${USER_GROUP} ${APACHE_RUN_GROUP} > /dev/null 2>&1



## Work Dir is /var/www
#cd /var/www/
if [ ! -f ${HOME}/docker/initialized ]; then
   bootup
fi

#echo $(ls /var/log/apache2)

#source /etc/apache2/envvars
#tail -F /var/log/apache2/* &
#rm -f /var/run/apache2/apache2.pid

exec apache2 -D FOREGROUND
