FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    bash \
    vim \
    jq \
    su-exec

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy custom PHP configuration
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy PHP-FPM pool tuning
COPY zz-performance.conf /usr/local/etc/php-fpm.d/zz-performance.conf

# Copy entrypoint and permission fix scripts
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
COPY fix-permissions.sh /usr/local/bin/fix-permissions.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/fix-permissions.sh && \
    sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && \
    sed -i 's/\r$//' /usr/local/bin/fix-permissions.sh

# Set working directory
WORKDIR /var/www

# Create system user to run Composer and Artisan Commands
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000

# Use entrypoint to fix permissions before starting php-fpm
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
