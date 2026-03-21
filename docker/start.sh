#!/bin/bash
set -e

echo "=== TornOps Container Starting ==="

echo "Running database initialization..."
/usr/local/bin/init-db.sh

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
