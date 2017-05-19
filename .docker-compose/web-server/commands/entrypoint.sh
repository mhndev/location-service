#!/bin/bash

set -e


if [ "${USER_ID}:${USER_GROUP}" != "0:0" ]; then
    exec gosu "${USER_ID}:${USER_GROUP}" "$@"
fi


# If GOSU_USER was 0:0 exec command passed in args without gosu (assume already root)
exec "$@"
