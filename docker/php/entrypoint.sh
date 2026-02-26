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

    # Performance: use file driver for cache & sessions (avoids extra DB queries per request)
    sed -i 's/CACHE_STORE=database/CACHE_STORE=file/' /var/www/.env
    sed -i 's/SESSION_DRIVER=database/SESSION_DRIVER=file/' /var/www/.env
    # In case keys don't exist in .env.example, append them
    grep -q "^CACHE_STORE=" /var/www/.env || echo "CACHE_STORE=file" >> /var/www/.env
    grep -q "^SESSION_DRIVER=" /var/www/.env || echo "SESSION_DRIVER=file" >> /var/www/.env
fi

# Auto-setup: Dependencies
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "Installing dependencies..."
    composer install --no-progress --no-interaction --working-dir=/var/www
fi

# Always ensure performance-oriented drivers (safe to run even if .env exists)
echo "Ensuring optimized cache/session drivers..."
sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/' /var/www/.env 2>/dev/null || true
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' /var/www/.env 2>/dev/null || true
sed -i 's/^APP_ENV=.*/APP_ENV=production/' /var/www/.env 2>/dev/null || true
grep -q "^CACHE_STORE=" /var/www/.env 2>/dev/null || echo "CACHE_STORE=file" >> /var/www/.env
grep -q "^SESSION_DRIVER=" /var/www/.env 2>/dev/null || echo "SESSION_DRIVER=file" >> /var/www/.env
grep -q "^APP_ENV=" /var/www/.env 2>/dev/null || echo "APP_ENV=production" >> /var/www/.env

# Auto-setup: App Key
if grep -q "APP_KEY=" /var/www/.env && [ -z "$(grep "APP_KEY=" /var/www/.env | cut -d '=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Auto-setup: Migrations
echo "Checking and running migrations..."
php artisan migrate --force --no-interaction

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

# Cache config, routes, views — eliminates 200+ file reads per request
echo "Warming up application caches (config + routes + views)..."
php artisan optimize:clear --quiet
php artisan optimize
php artisan event:cache 2>/dev/null || true

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
