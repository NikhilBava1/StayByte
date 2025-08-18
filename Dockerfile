# Official PHP 8.2 with Apache image
FROM php:8.2-apache

# Enable Apache mod_rewrite (optional, if your app needs it)
RUN a2enmod rewrite

# Install system dependencies for PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev

# Install PHP extensions for PostgreSQL support
RUN docker-php-ext-install pgsql pdo pdo_pgsql

# Copy your entire project into Apache's root directory
COPY . /var/www/html/

# Set ownership so Apache can access files
RUN chown -R www-data:www-data /var/www/html/

# Expose port 80 (default HTTP port)
EXPOSE 80
