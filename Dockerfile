FROM php:8.2-apache

# Install SQLite dependencies
RUN apt-get update && apt-get install -y libsqlite3-dev && \
    docker-php-ext-install pdo pdo_sqlite

# Copy project files
COPY . /var/www/html/

# Set permissions for the database directory
RUN mkdir -p /var/www/html/database && chmod -R 777 /var/www/html/database

# Change Apache port to 80 (standard)
EXPOSE 80
