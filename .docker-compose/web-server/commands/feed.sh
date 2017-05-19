#!/bin/bash

printf "\033[0;32m > Feed Location Indexes Into Elastic ...\x1b[0m \n"
php /var/www/index_elastic.php > /dev/null 2>&1
printf "\033[0;32m > Feed Complete !\x1b[0m \n"
