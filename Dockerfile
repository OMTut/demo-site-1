FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libexif-dev \
    libonig-dev \
    libwebp-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Drupal
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        opcache \
        intl \
        exif

# Enable Apache mod_rewrite (required for Drupal clean URL)
RUN a2enmod rewrite

# Set Apache to serve from the Drupal docroot (web/)
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf

# Install Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy dependency manifests first to leverage Docker layer caching
COPY composer.json composer.lock ./

# Install PHP dependencies (no dev packages, optimised autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application code
COPY config/ config/
COPY web/themes/custom/ web/themes/custom/

# Copy production settings (overrides the scaffold default.settings.php)
COPY docker/settings.php web/sites/default/settings.php

# Create the public files mount point and set ownership
RUN mkdir -p web/sites/default/files \
    && chown -R www-data:www-data web/sites \
    && chmod -R 755 web/sites

EXPOSE 80
