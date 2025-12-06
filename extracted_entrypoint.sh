#!/bin/bash
set -e

# Handle multiple domains in SERVER_NAME
# Split SERVER_NAME into array
IFS=' ' read -r -a SERVERS <<< "$SERVER_NAME" || true

# First domain is the actual ServerName
export SERVER_NAME="${SERVERS[0]}"

# The rest (plus existing SERVER_ALIAS) become ServerAlias
if [ "${#SERVERS[@]}" -gt 1 ]; then
    ALIASES="${SERVERS[@]:1}"
    if [ -n "$SERVER_ALIAS" ]; then
        export SERVER_ALIAS="$ALIASES $SERVER_ALIAS"
    else
        export SERVER_ALIAS="$ALIASES"
    fi
fi

# Substitute environment variables in Apache config
envsubst '${SERVER_NAME} ${SERVER_ALIAS}' < /etc/apache2/sites-available/000-default.conf.template > /etc/apache2/sites-available/000-default.conf

# Start Redis
service redis-server start

# Start MariaDB
# Fix permissions (in case volume was mounted from host or another container)
chown -R mysql:mysql /var/lib/mysql
mkdir -p /run/mysqld
chown -R mysql:mysql /run/mysqld

# We need to initialize if it's new
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "Initializing MariaDB..."
    mysql_install_db --user=mysql --datadir=/var/lib/mysql
fi

# Start MariaDB in background to configure it
mysqld_safe --datadir=/var/lib/mysql &
MYSQL_PID=$!

# Wait for MariaDB to be ready
echo "Waiting for MariaDB..."
until mysqladmin ping -h localhost --silent; do
    sleep 1
done

# Configure MariaDB (Create DB and User if env provided)
if [ -n "$MYSQL_DATABASE" ]; then
    echo "Creating database: $MYSQL_DATABASE"
    mysql -e "CREATE DATABASE IF NOT EXISTS \`$MYSQL_DATABASE\`;"
fi

if [ -n "$MYSQL_USER" ] && [ -n "$MYSQL_PASSWORD" ]; then
    echo "Creating user: $MYSQL_USER"
    mysql -e "CREATE USER IF NOT EXISTS '$MYSQL_USER'@'localhost' IDENTIFIED BY '$MYSQL_PASSWORD';"
    mysql -e "GRANT ALL PRIVILEGES ON \`$MYSQL_DATABASE\`.* TO '$MYSQL_USER'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"
fi

# Start Apache via official entrypoint (handles Copy of WP files)
echo "Starting WordPress (Apache)..."
source /etc/apache2/envvars
# docker-entrypoint.sh expects the command as arguments
exec /usr/local/bin/docker-entrypoint.sh apache2-foreground
