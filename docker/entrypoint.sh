#!/bin/sh
set -e

PORT="${PORT:-10000}"

echo "Configuration de nginx sur le port ${PORT}..."
envsubst '${PORT}' < /etc/nginx/sites-available/default > /tmp/default.conf
mv /tmp/default.conf /etc/nginx/sites-enabled/default

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Application des migrations..."
    php database/migrate.php
fi

exec "$@"
