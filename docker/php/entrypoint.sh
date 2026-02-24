#!/bin/sh
set -e

echo "Starting entrypoint script..."

# Auto-setup: .env
if [ ! -f "/var/www/.env" ]; then
    echo "Creating .env file..."
    cp /var/www/.env.example /var/www/.env
    
    # Configure Database for Docker (MySQL)
    echo "Configuring .env for Docker (MySQL)..."
    sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' /var/www/.env
    sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=db/' /var/www/.env
    sed -i 's/# DB_PORT=3306/DB_PORT=3306/' /var/www/.env
    sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=laravel/' /var/www/.env
    sed -i 's/# DB_USERNAME=root/DB_USERNAME=laravel/' /var/www/.env
    sed -i 's/# DB_PASSWORD=/DB_PASSWORD=secret/' /var/www/.env
fi

# Auto-setup: Dependencies
if [ ! -d "/var/www/vendor" ]; then
    echo "Installing dependencies..."
    composer install --no-progress --no-interaction
fi

# Auto-setup: App Key
if grep -q "APP_KEY=" /var/www/.env && [ -z "$(grep "APP_KEY=" /var/www/.env | cut -d '=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Auto-setup: Migrations (Wait for DB)
echo "Running migrations..."
php artisan migrate --force

# Auto-setup: Seed only on first boot
if [ ! -f "/var/www/storage/.seeded" ]; then
    echo "Seeding database with demo data..."
    php artisan db:seed --force && touch /var/www/storage/.seeded
fi

# Auto-setup: Storage link
if [ ! -L "/var/www/public/storage" ]; then
    echo "Creating storage symlink..."
    php artisan storage:link
fi

# Reset permissions just in case
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
