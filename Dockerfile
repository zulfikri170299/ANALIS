FROM php:8.2-apache

# Menginstal dependensi sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Menghapus cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Menginstal ekstensi PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Mengaktifkan mod_rewrite Apache untuk Laravel
RUN a2enmod rewrite

# Mengubah konfigurasi DocumentRoot Apache agar mengarah ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Menginstal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Menetapkan direktori kerja
WORKDIR /var/www/html

# Menyalin file proyek
COPY . .

# Memberikan hak akses ke folder storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Menghapus composer.lock yang bermasalah dan menjalankan install ulang
RUN rm -f composer.lock && composer install --optimize-autoloader --no-dev

# Aset Vite akan dikompilasi secara lokal dan disertakan dalam repositori

EXPOSE 80
