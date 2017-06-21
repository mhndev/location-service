#!/bin/bash

set -e

if [[ -d "/var/www/public" && !(-L "/var/www/html" || -d "/var/www/html") ]]; then
  ln -s /var/www/public /var/www/html
fi

# Using this UID / GID allows local and live file modification of web site

usermod -u ${USER_ID} ${APACHE_RUN_USER} > /dev/null 2>&1
groupmod -g ${USER_GROUP} ${APACHE_RUN_GROUP} > /dev/null 2>&1


# chown -R ${APACHE_RUN_USER}:${APACHE_RUN_GROUP} /var/log/apache2

# todo (what to do what not to do :) ?)
#chmod -R 777 /var/log/apache2
#
#ls -l /var/log

source /etc/apache2/envvars
#tail -F /var/log/apache2/* &
rm -f /var/run/apache2/apache2.pid


/usr/sbin/apache2 -D FOREGROUND

# su -s /bin/bash -c "/usr/sbin/apache2 -D FOREGROUND" www-data
