FROM php:8.2-cli

RUN apt-get update && apt-get install -y unzip libzip-dev zip libpq-dev \
    && docker-php-ext-install zip pdo pdo_pgsql

WORKDIR /app

COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php && php composer.phar install --no-dev --optimize-autoloader

COPY . .

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "php_app/public"]
