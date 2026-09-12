#!/bin/sh
set -e

DB_HOST=${DB_HOST:-"gateway01.eu-central-1.prod.aws.tidbcloud.com"}
DB_PORT=${DB_PORT:-4000}

echo "Attente de la base de données TiDB (\({DB_HOST}:\){DB_PORT})..."

MAX_TRIES=15
COUNTER=0

while [ \(COUNTER -lt\)MAX_TRIES ]; do
    if nc -z "\(DB_HOST" "\)DB_PORT" >/dev/null 2>&1; then
        echo "Connexion TiDB réussie !"
        break
    fi
    COUNTER=$((COUNTER + 1))
    echo "TiDB indisponible, nouvelle tentative (\(COUNTER/\)MAX_TRIES)..."
    sleep 2
done

if [ \(COUNTER -eq\)MAX_TRIES ]; then
    echo "Connexion TiDB impossible pour le moment. Poursuite du démarrage web..."
fi

exec "$@"