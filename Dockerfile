# Use an official PHP image with Composer pre-installed
FROM php:8.2-cli

# Install dependencies (like unzip for composer)
RUN apt-get update && apt-get install -y unzip libzip-dev zip && docker-php-ext-install zip

# Set working directory
WORKDIR /app

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php && php composer.phar install --no-dev --optimize-autoloader

# Copy the rest of the app
COPY . .

# Expose port (if using built-in server)
EXPOSE 8000

# Start the PHP built-in server (adjust path/port if needed)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "php_app/public"]
