# Use the official PHP image with Apache as the base image
FROM php:8.2-apache

# Install necessary extensions for PHP
RUN docker-php-ext-install pdo pdo_mysql

# Install required tools for Composer and other PHP extensions
RUN apt-get update && apt-get install -y \
    libmariadb-dev-compat \ 
    libmariadb-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the custom Apache configuration
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache mod_rewrite (optional but useful for many PHP projects)
RUN a2enmod rewrite

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the source code into the container
COPY ./src /var/www/html

# Expose port 80 to allow the container to serve web traffic
EXPOSE 80

# Set the default command to run Apache in the foreground
CMD ["apache2-foreground"]
