#!/bin/bash

if [ ! -f /docker/initialized ]; then
   bootup
fi

php /var/www/index_elastic.php

