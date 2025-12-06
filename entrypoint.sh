#!/bin/bash
set -e

# Substitute environment variables in Apache config
envsubst '${SERVER_NAME} ${SERVER_ALIAS}' < /etc/apache2/sites-available/000-default.conf.template > /etc/apache2/sites-available/000-default.conf

# Start Redis
service redis-server start

# Start MariaDB
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

# Start Apache in foreground
echo "Starting Apache..."
source /etc/apache2/envvars
exec apache2 -D FOREGROUND
