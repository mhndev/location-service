#!/bin/bash

rm /docker/initialized

composer install --ignore-platform-reqs --no-dev
cp /docker/app/.env ./

chmod 0777 -R /data/logs/

touch /docker/initialized