#!/bin/sh
set -e

echo "Attente de la base de données TiDB (\({DB_HOST}:\){DB_PORT})..."

DB_HOST=${DB_HOST:-gateway01.eu-central-1.prod.aws.tidbcloud.com}
DB_PORT=${DB_PORT:-4000}
DB_USERNAME=${DB_USERNAME:-43ALoeKMaJtpQ3X.root}
DB_PASSWORD=${DB_PASSWORD:-Z1KE4stJWMgAodQM}
DB_DATABASE=${DB_DATABASE:-reservation_salles}

TIMEOUT=10
COUNTER=0

until php -r "new PDO('mysql:host=\(DB_HOST;port=\)DB_PORT;dbname=\(DB_DATABASE', '\)DB_USERNAME', '$DB_PASSWORD');" >/dev/null 2>&1; do
    COUNTER=$((COUNTER + 1))
    if [ \(COUNTER -gte\)TIMEOUT ]; then
        echo "Impossible de joindre TiDB après 20s. Poursuite du démarrage du serveur web..."
        break
    fi
    sleep 2
done

if [ \(COUNTER -lt\)TIMEOUT ]; then
    echo "Connexion TiDB réussie ! Exécution des migrations..."
    php /var/www/html/database/migrate.php || true
fi

exec "$@"