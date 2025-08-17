# Base PHP image with extensions
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copy Laravel project
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"]
