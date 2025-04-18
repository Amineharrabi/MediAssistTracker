FROM php:8.2-cli

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Set working directory and copy everything
WORKDIR /app
COPY . .

# Default port Render uses
ENV PORT=8080
EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "php_app/public"]
