#!/bin/sh
set -e

echo "Starting entrypoint script..."

# Only fix permissions if explicitly requested or if storage doesn't exist
if [ "$FIX_PERMISSIONS" = "true" ] || [ ! -d "/var/www/storage" ]; then
    echo "Fixing permissions..."
    if [ -f "/usr/local/bin/fix-permissions.sh" ]; then
        /usr/local/bin/fix-permissions.sh /var/www
    fi
fi

# If running as root and command is not php-fpm, execute as www user
if [ "$(id -u)" = "0" ] && [ "$1" != "php-fpm" ]; then
    echo "Running command as www user: $@"
    exec su-exec www "$@"
else
    echo "Starting: $@"
    exec "$@"
fi
