FROM php:8.2-cli

# Install dependencies for PostgreSQL and system tools
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    zip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Set working directory and copy project files
WORKDIR /app
COPY . .

# Set the port for Render
ENV PORT=8080
EXPOSE 8080

# Start the PHP server from the public folder
CMD ["php", "-S", "0.0.0.0:8080", "-t", "php_app/public"]
