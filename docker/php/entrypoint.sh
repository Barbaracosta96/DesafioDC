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
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "Installing dependencies..."
    if [ -f "/var/www/composer.lock" ]; then
        composer install --no-progress --no-interaction
    else
        composer update --no-progress --no-interaction
    fi
fi

# Auto-setup: App Key
if grep -q "APP_KEY=" /var/www/.env && [ -z "$(grep "APP_KEY=" /var/www/.env | cut -d '=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Auto-setup: Migrations (Wait for DB)
echo "Running migrations..."
php artisan migrate --force || echo "Warning: migrate had errors; check DB state"

# Run seeders if DB is fresh (zero records in users table)
USER_COUNT=$(php artisan tinker --execute="echo \\App\\Models\\User::count();" 2>/dev/null | tail -1 | tr -d '\r\n' || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "DB vazia - executando seeders..."
    php artisan db:seed --force || echo "Warning: seed had errors"
fi

# Producao: gera caches de configuracao, rotas e views para maximo desempenho
echo "Gerando caches de producao (config, route, view, events)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✅ Caches gerados com sucesso!"

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
