FROM php:5.6-apache

# Alihkan repositori Debian Stretch yang sudah EOL ke archive.debian.org
RUN echo "deb http://archive.debian.org/debian/ stretch main" > /etc/apt/sources.list && \
    echo "deb http://archive.debian.org/debian-security/ stretch/updates main" >> /etc/apt/sources.list && \
    echo "Acquire::Check-Valid-Time \"false\";" > /etc/apt/apt.conf.d/99no-check-valid-time

# Install ekstensi MySQL dan Mcrypt dengan izin unauthenticated karena repo arsip
RUN apt-get update && apt-get install -y --allow-unauthenticated libmcrypt-dev \
    && docker-php-ext-install mysqli mysql pdo pdo_mysql mcrypt

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

WORKDIR /var/www/html