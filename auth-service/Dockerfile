FROM php:8.4-fpm-alpine

# Set working directory
WORKDIR /var/www

# Install essential APK packages for Laravel and extensions
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    git \
    nodejs \
    npm

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    gd

# Install global Composer from the latest official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application source code
COPY . /var/www

# Run composer install and optimize the autoloader
RUN composer install --no-scripts --no-autoloader \
    && composer dump-autoload --optimize

# Expose port 8000
EXPOSE 8000

# Default command to run the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
