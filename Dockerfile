# Gunakan Image PHP 7.4 dengan Apache
FROM php:7.4-apache

# 1. Install Library Sistem yang dibutuhkan Laravel lama
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# 2. Aktifkan mod_rewrite Apache (Wajib buat Laravel)
RUN a2enmod rewrite

# 3. Ubah Document Root ke folder /public (Standar Laravel)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 4. Copy semua file project ke dalam container
WORKDIR /var/www/html
COPY . .

# 5. Install Composer (Versi 2.x aman untuk PHP 7.4)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Install Dependency Laravel via Composer
# Kita abaikan platform check biar tidak rewel soal versi
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 7. Atur Hak Akses Folder Storage (PENTING biar tidak error permission)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Expose Port (Railway otomatis inject $PORT, tapi kita buka 80 default)
EXPOSE 80