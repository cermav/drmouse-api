# Use an official PHP runtime as a parent image
FROM lorisleiva/laravel-docker:7.1

# Set working directory to /var/www/html
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.0

# Clone your Laravel application
COPY . .

# Copy Apache virtual host configuration
#COPY ./apache-config.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install and configure MySQL
## RUN apt-get install -y mariadb-server
#COPY ./my.cnf /etc/mysql/my.cnf

# Install phpMyAdmin
#RUN apt-get install -y phpmyadmin
#COPY ./config.inc.php /etc/phpmyadmin/config.inc.php

# RUN composer config -g -- disable-tls false

RUN composer install --prefer-dist --no-scripts -q -o
#RUN composer update
# RUN composer install

# Expose ports 80 and 3306
EXPOSE 80
## EXPOSE 3306

# Start Apache and MySQL services
##CMD /etc/init.d/mysql start
CMD /usr/sbin/apache2ctl -D FOREGROUND
