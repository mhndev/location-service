#!/bin/bash

if [ ! -f $HOME/docker/initialized ]; then
   bootup
fi

composer update --ignore-platform-reqs --no-dev
cp /docker/app/.env ./
