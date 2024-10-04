# Start from the PHP 8.3 image
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    git \
    unzip \
    && docker-php-ext-install zip \
    && docker-php-ext-install openssl

# Install the MongoDB extension
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Set up php.ini
RUN echo "extension=mongodb.so" >> /usr/local/etc/php/conf.d/docker-php-ext-mongodb.ini

# Set the working directory
WORKDIR /var/www

# Copy the existing application directory contents
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose the port the app runs on
EXPOSE 8080

# Command to run the Laravel application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
