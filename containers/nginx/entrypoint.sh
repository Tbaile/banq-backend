#!/usr/bin/env sh

set -e

if [ "$1" = 'nginx' ]; then
    wait-for "${FPM_URL:?Missing FPM_URL}:${FPM_PORT:?Missing FPM_PORT}" -t 60
fi
exec /docker-entrypoint.sh "$@"
