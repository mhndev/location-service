#!/bin/bash

rm /docker/initialized

composer install --ignore-platform-reqs --no-dev
cp /docker/app/.env ./

touch /docker/initialized