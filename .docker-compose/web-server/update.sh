#!/bin/bash

if [ ! -f /docker/initialized ]; then
   bootup
fi

composer update --ignore-platform-reqs --no-dev
cp /docker/app/.env ./
