#!/bin/sh
set -e

if [ -f /var/www/html/.env ]; then
    set -a
    . /var/www/html/.env
    set +a
fi

echo "Attente de MySQL (${DB_HOST}:${DB_PORT})..."
until php /var/www/html/docker/wait-for-db.php "$DB_HOST" "$DB_PORT" "$DB_USERNAME" "$DB_PASSWORD" >/dev/null 2>&1; do
    sleep 2
done

echo "MySQL prêt : application des migrations..."
php /var/www/html/database/migrate.php

echo "Insertion des données initiales..."
php /var/www/html/database/seed.php

echo "Initialisation terminée : démarrage de PHP-FPM."
exec "$@"