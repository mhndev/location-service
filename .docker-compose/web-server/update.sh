#!/bin/bash

composer update --ignore-platform-reqs --no-dev
cp /docker/app/.env ./
