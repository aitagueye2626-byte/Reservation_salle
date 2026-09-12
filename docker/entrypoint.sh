#!/bin/sh
set -e

DB_HOST="${DB_HOST:-gateway01.eu-central-1.prod.aws.tidbcloud.com}"
DB_PORT="${DB_PORT:-4000}"
DB_USERNAME="${DB_USERNAME:-43ALoeKMaJtpQ3X.root}"
DB_PASSWORD="${DB_PASSWORD:-Z1KE4stJWMgAodQM}"
DB_DATABASE="${DB_DATABASE:-reservation_salles}"

echo "Attente de la base de données TiDB (\({DB_HOST}:\){DB_PORT})..."

i=0
connected=0

while [ $i -lt 5 ]; do
    if php -r "new PDO('mysql:host=\(DB_HOST;port=\)DB_PORT;dbname=\(DB_DATABASE', '\)DB_USERNAME', '$DB_PASSWORD');" >/dev/null 2>&1; then
        connected=1
        break
    fi
    i=$((i + 1))
    sleep 2
done

if [ $connected -eq 1 ]; then
    echo "Connexion TiDB réussie ! Exécution des migrations..."
    php /var/www/html/database/migrate.php || true
else
    echo "Connexion TiDB impossible pour le moment. Poursuite du démarrage web..."
fi

exec "$@"