# Use the official PHP image as a base
FROM php:8.3-fpm

# Install MongoDB PHP driver
RUN apt-get update && apt-get install -y libcurl4-openssl-dev pkg-config libssl-dev && \
    pecl install mongodb && docker-php-ext-enable mongodb

# Set working directory
WORKDIR /var/www

# Copy the application code
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose the port the app runs on
EXPOSE 8080

# Start PHP-FPM server
CMD ["php-fpm"]
