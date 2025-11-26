# Gunakan Image PHP 8.2 dengan Apache
FROM php:8.2-apache

# 1. Install Ekstensi Wajib & NodeJS
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mysqli mbstring zip exif pcntl bcmath gd opcache

# 2. Aktifkan Mod Rewrite (Wajib untuk .htaccess)
RUN a2enmod rewrite

# 3. Atur Document Root ke /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 4. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Set Working Directory
WORKDIR /var/www/html

# 6. Copy Semua File Project
COPY . .

# 7. Install Dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install && npm run build

# 8. PERBAIKAN DISINI: Buat folder storage dulu, baru beri izin
RUN mkdir -p /var/www/html/storage/logs \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/templates \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage

# 9. Port
EXPOSE 80