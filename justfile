# Laravel Docker Development Commands
# Compatible with Linux, macOS, and Windows

# Detect OS and set appropriate commands
is_windows := if os() == "windows" { "true" } else { "false" }
is_linux := if os() == "linux" { "true" } else { "false" }
is_macos := if os() == "macos" { "true" } else { "false" }

# Docker Compose command (auto-detects if sudo is needed)
dc := if is_windows == "true" {
    "docker-compose"
} else if is_macos == "true" {
    "docker-compose"
} else {
    "sudo docker-compose"
}

# Shell command
shell_cmd := if is_windows == "true" { "sh" } else { "sh" }

# Docker Management
# ==================

# Start all containers in detached mode (auto-fix permissions)
up:
    {{dc}} up -d
    @echo "Waiting for containers to be ready..."
    @sleep 3
    @echo "Fixing Laravel permissions..."
    {{dc}} exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database || true
    {{dc}} exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/database || true
    @echo "Clearing view cache..."
    {{dc}} exec app php artisan view:clear || true
    @echo "Containers ready! Access http://localhost"

# Stop all containers
down:
    {{dc}} down

# Restart all containers
restart:
    {{dc}} restart

# Show logs for all services or specific service
logs service="":
    @if [ -z "{{service}}" ]; then \
        {{dc}} logs -f; \
    else \
        {{dc}} logs -f {{service}}; \
    fi

# Show status of all containers
ps:
    {{dc}} ps

# Rebuild containers
rebuild:
    {{dc}} down
    {{dc}} build --no-cache
    {{dc}} up -d

# Build containers without cache
build:
    {{dc}} build --no-cache

# Laravel Setup (First Time)
# ==========================

# Complete first-time setup (create Laravel project and configure)
install:
    @echo "Creating Laravel project..."
    {{dc}} exec app composer create-project laravel/laravel tmp
    @echo "Moving files with proper merge..."
    {{dc}} exec app sh -c "cp -rf tmp/. . && rm -rf tmp"
    @echo "Configuring environment..."
    {{dc}} exec app cp .env.example .env || true
    {{dc}} exec app php artisan key:generate
    @echo "Updating .env for Docker..."
    {{dc}} exec app sed -i 's/DB_HOST=127.0.0.1/DB_HOST=db/' .env
    {{dc}} exec app sed -i 's/DB_DATABASE=laravel/DB_DATABASE=laravel/' .env
    {{dc}} exec app sed -i 's/DB_USERNAME=root/DB_USERNAME=laravel/' .env
    {{dc}} exec app sed -i 's/DB_PASSWORD=/DB_PASSWORD=secret/' .env
    @echo "Running migrations..."
    {{dc}} exec app php artisan migrate
    @echo "Installing npm dependencies..."
    {{dc}} exec node npm install
    @echo "Fixing permissions..."
    just fix-permissions
    @echo "Setup complete! Access http://localhost"

# Quick install (if src directory already exists with Laravel)
setup:
    {{dc}} exec app composer install
    {{dc}} exec app cp .env.example .env || true
    {{dc}} exec app php artisan key:generate
    {{dc}} exec app php artisan migrate
    {{dc}} exec node npm install
    just fix-permissions

# Laravel Commands
# ================

# Run artisan commands (usage: just artisan migrate)
artisan *args:
    {{dc}} exec app php artisan {{args}}

# Run composer commands (usage: just composer require package)
composer *args:
    {{dc}} exec app composer {{args}}

# Run npm commands (usage: just npm install)
npm *args:
    {{dc}} exec node npm {{args}}

# Database Commands
# =================

# Run database migrations
migrate:
    {{dc}} exec app php artisan migrate

# Fresh migration with seeding
fresh:
    {{dc}} exec app php artisan migrate:fresh --seed

# Rollback last migration
rollback:
    {{dc}} exec app php artisan migrate:rollback

# Create a new migration (usage: just make-migration create_users_table)
make-migration name:
    {{dc}} exec app php artisan make:migration {{name}}

# Seed the database
seed:
    {{dc}} exec app php artisan db:seed

# Create a new model (usage: just make-model Post)
make-model name *args:
    {{dc}} exec app php artisan make:model {{name}} {{args}}

# Create a new controller (usage: just make-controller PostController)
make-controller name:
    {{dc}} exec app php artisan make:controller {{name}}

# Testing
# =======

# Run PHPUnit tests
test *args:
    {{dc}} exec app php artisan test {{args}}

# Run specific test file
test-file file:
    {{dc}} exec app php artisan test {{file}}

# Code Quality
# ============

# Run Laravel Pint (code formatter)
pint:
    {{dc}} exec app ./vendor/bin/pint

# Clear all Laravel caches
clear:
    {{dc}} exec app php artisan cache:clear
    {{dc}} exec app php artisan config:clear
    {{dc}} exec app php artisan route:clear
    {{dc}} exec app php artisan view:clear

# Optimize Laravel for production
optimize:
    {{dc}} exec app php artisan config:cache
    {{dc}} exec app php artisan route:cache
    {{dc}} exec app php artisan view:cache

# Shell Access
# ============

# Access shell in specific container (usage: just shell app)
shell service:
    {{dc}} exec {{service}} {{shell_cmd}}

# Access bash in app container
bash:
    {{dc}} exec app bash || {{dc}} exec app sh

# Access MySQL CLI
mysql:
    {{dc}} exec db mysql -u laravel -psecret laravel

# Access MySQL as root
mysql-root:
    {{dc}} exec db mysql -u root -proot

# Maintenance
# ===========

# Clean up everything (containers, volumes, dependencies)
clean:
    @echo "Stopping containers..."
    {{dc}} down -v
    @echo "Removing dependencies..."
    @if [ "{{is_windows}}" = "true" ]; then \
        if exist src\\vendor rmdir /s /q src\\vendor; \
        if exist src\\node_modules rmdir /s /q src\\node_modules; \
    else \
        rm -rf src/vendor src/node_modules; \
    fi
    @echo "Clean complete!"

# Full reset (clean + rebuild)
reset: clean rebuild

# Reset database volume only (when MySQL won't start)
reset-db:
    @echo "Stopping database container..."
    {{dc}} stop db || true
    @echo "Removing database container and volume..."
    {{dc}} rm -f db || true
    @if [ "{{is_windows}}" = "true" ]; then \
        docker volume rm desafio_mysql_data 2>/dev/null || echo "Volume already removed"; \
    elif [ "{{is_macos}}" = "true" ]; then \
        docker volume rm desafio_mysql_data 2>/dev/null || echo "Volume already removed"; \
    else \
        sudo docker volume rm desafio_mysql_data 2>/dev/null || echo "Volume already removed"; \
    fi
    @echo "Database reset complete! Run 'just up' to start again."

# Remove all unused Docker resources
prune:
    @echo "Pruning Docker system..."
    @if [ "{{is_windows}}" = "true" ]; then \
        docker system prune -af --volumes; \
    elif [ "{{is_macos}}" = "true" ]; then \
        docker system prune -af --volumes; \
    else \
        sudo docker system prune -af --volumes; \
    fi

# Development
# ===========

# Start dev server (up + logs)
dev:
    just up
    {{dc}} logs -f

# Watch logs for specific service
watch service:
    {{dc}} logs -f {{service}}

# Quick restart of a service (usage: just restart-service app)
restart-service service:
    {{dc}} restart {{service}}

# Fix permissions for Laravel storage, cache, and database directories
fix-permissions:
    @echo "Fixing Laravel permissions..."
    {{dc}} exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database
    {{dc}} exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/database
    @echo "Clearing view cache..."
    {{dc}} exec app php artisan view:clear || true
    @echo "Permissions fixed successfully!"

# Quick fix for permission errors (restart + fix)
quick-fix:
    @echo "Restarting app container..."
    {{dc}} restart app
    @echo "Waiting for app to be ready..."
    @sleep 2
    @echo "Fixing permissions..."
    {{dc}} exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database
    {{dc}} exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/database
    @echo "Clearing all caches..."
    {{dc}} exec app php artisan view:clear || true
    {{dc}} exec app php artisan config:clear || true
    {{dc}} exec app php artisan cache:clear || true
    @echo "Quick fix complete! Try http://localhost"

# Info
# ====

# Show environment info
info:
    @echo "=== Environment Information ==="
    @echo "OS: {{os()}}"
    @echo "OS Family: {{os_family()}}"
    @echo "Docker Compose Command: {{dc}}"
    @echo ""
    @echo "=== Docker Status ==="
    @{{dc}} ps
    @echo ""
    @echo "=== Available Commands ==="
    @just --list

# View help
help:
    @just --list

# Default recipe (shows help)
default:
    @just --list
