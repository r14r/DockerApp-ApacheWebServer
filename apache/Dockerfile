FROM php:8.3-apache

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    ca-certificates \
    unzip \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libicu-dev \
    libzip-dev \
    libxml2-dev \
    libonig-dev \
    libcurl4-openssl-dev \
 && rm -rf /var/lib/apt/lists/*

 RUN docker-php-ext-install pdo pdo_mysql mysqli

 RUN a2enmod rewrite headers expires

 WORKDIR /var/www/html

COPY php.ini     /usr/local/etc/php/conf.d/zz-overrides.ini
COPY apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]