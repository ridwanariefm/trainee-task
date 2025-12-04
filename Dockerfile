# Gunakan Image PHP 7.4 dengan Apache
FROM php:7.4-apache

# 1. Install Library Sistem
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

# 2. Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# 3. Ubah Document Root ke folder public dan Izinkan .htaccess (FIX UTAMA)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# === BAGIAN PENTING: Paksa AllowOverride All agar route Laravel jalan ===
RUN echo '<Directory /var/www/html/public/>' > /etc/apache2/conf-available/override.conf \
    && echo '    AllowOverride All' >> /etc/apache2/conf-available/override.conf \
    && echo '    Require all granted' >> /etc/apache2/conf-available/override.conf \
    && echo '</Directory>' >> /etc/apache2/conf-available/override.conf \
    && a2enconf override

# 4. Copy semua file project
WORKDIR /var/www/html
COPY . .

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Install Dependency Laravel
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 7. Atur Hak Akses Folder Storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Expose Port
EXPOSE 80