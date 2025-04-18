FROM php:8.2-cli

# Install system dependencies and PostgreSQL PDO driver
RUN apt-get update && apt-get install -y unzip libzip-dev zip libpq-dev \
    && docker-php-ext-install zip pdo pdo_pgsql

# Set working directory
WORKDIR /app

# Install Composer dependencies
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php && php composer.phar install --no-dev --optimize-autoloader

# Copy rest of the app
COPY . .

# Expose port and start the PHP built-in server
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "php_app/public"]
