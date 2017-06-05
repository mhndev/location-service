#!/bin/bash

set -e

# Using this UID / GID allows local and live file modification of web site

usermod -u ${USER_ID} ${APACHE_RUN_USER} > /dev/null 2>&1
groupmod -g ${USER_GROUP} ${APACHE_RUN_GROUP} > /dev/null 2>&1



## Work Dir is /var/www
#cd /var/www/
if [ ! -f /var/www/vendor ]; then
   bootup
fi

# chown -R ${APACHE_RUN_USER}:${APACHE_RUN_GROUP} /var/log/apache2

# todo (what to do what not to do :) ?)
# chmod -R 777 /var/log/apache2

source /etc/apache2/envvars
tail -F /var/log/apache2/* &
rm -f /var/run/apache2/apache2.pid


/usr/sbin/apache2 -D FOREGROUND

# su -s /bin/bash -c "/usr/sbin/apache2 -D FOREGROUND" www-data
