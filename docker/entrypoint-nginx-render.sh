#!/bin/sh
set -e

echo "Configuration de nginx (port ${PORT}, backend ${APP_HOST})..."
envsubst '${PORT} ${APP_HOST}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

exec "$@"