#!/bin/sh
# Script to fix Laravel permissions in Docker
# Works across Linux, macOS, and Windows

echo "Fixing Laravel permissions..."

# Get the working directory
WORKDIR=${1:-/var/www}

# Check if directory exists
if [ ! -d "$WORKDIR" ]; then
    echo "Directory $WORKDIR does not exist"
    exit 0
fi

# Fix ownership - set to www:www (UID:GID 1000:1000)
echo "Setting ownership to www:www..."
chown -R www:www "$WORKDIR" 2>/dev/null || true

# Fix base permissions
echo "Setting base permissions..."
find "$WORKDIR" -type f -exec chmod 664 {} \; 2>/dev/null || true
find "$WORKDIR" -type d -exec chmod 775 {} \; 2>/dev/null || true

# Function to ensure directory exists and has correct permissions
fix_dir() {
    dir="$1"
    if [ ! -d "$dir" ]; then
        echo "Creating directory: $dir"
        mkdir -p "$dir" 2>/dev/null || true
    fi

    if [ -d "$dir" ]; then
        echo "Setting permissions for: $dir"
        chown -R www:www "$dir" 2>/dev/null || true
        chmod -R 775 "$dir" 2>/dev/null || true
    fi
}

# Critical directories that need write access
echo "Ensuring writable directories have correct permissions..."
fix_dir "$WORKDIR/storage"
fix_dir "$WORKDIR/storage/framework"
fix_dir "$WORKDIR/storage/framework/cache"
fix_dir "$WORKDIR/storage/framework/sessions"
fix_dir "$WORKDIR/storage/framework/views"
fix_dir "$WORKDIR/storage/logs"
fix_dir "$WORKDIR/storage/app"
fix_dir "$WORKDIR/storage/app/public"
fix_dir "$WORKDIR/bootstrap/cache"

# Fix node_modules permissions for Vite
if [ -d "$WORKDIR/node_modules" ]; then
    echo "Fixing node_modules permissions..."
    chmod -R 755 "$WORKDIR/node_modules/.bin" 2>/dev/null || true
fi

# Make artisan executable
if [ -f "$WORKDIR/artisan" ]; then
    echo "Making artisan executable..."
    chmod +x "$WORKDIR/artisan" 2>/dev/null || true
fi

# Fix permissions for specific files that need execution
echo "Fixing executable permissions..."
find "$WORKDIR" -type f -name "artisan" -exec chmod +x {} \; 2>/dev/null || true
find "$WORKDIR" -type f -name "*.sh" -exec chmod +x {} \; 2>/dev/null || true

echo "Permissions fixed successfully!"
