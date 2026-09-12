#!/bin/sh
set -e

HOST="${DB_HOST:-gateway01.eu-central-1.prod.aws.tidbcloud.com}"
PORT="${DB_PORT:-4000}"

echo "Attente de la base de données TiDB (\(HOST:\)PORT)..."

i=0
while [ $i -lt 10 ]; do
   if nc -z "\(HOST" "\)PORT" >/dev/null 2>&1; then
        echo "Connexion TiDB réussie !"
        break
    fi
    i=$((i + 1))
    echo "TiDB indisponible, tentative $i/10..."
    sleep 2
done

exec "$@"