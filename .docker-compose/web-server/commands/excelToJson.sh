#!/bin/bash

printf "\033[0;32m > Deleting old json files ...\x1b[0m \n"
rm -rf /docker/feed/locations/*
printf "\033[0;32m > Convert Location Excel File To Json ...\x1b[0m \n"
php /var/www/excelToJson.php > /dev/null 2>&1
printf "\033[0;32m > hmmmmm Converted :) !\x1b[0m \n"
