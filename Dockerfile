# Official PHP 8.2 with Apache image
FROM php:8.2-apache

# Enable Apache mod_rewrite (optional, if your app needs it)
RUN a2enmod rewrite

# Install PHP extensions for MySQL support
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your entire project into Apache's root directory
COPY . /var/www/html/

# Set ownership so Apache can access files
RUN chown -R www-data:www-data /var/www/html/

# Expose port 80 (default HTTP port)
EXPOSE 80
