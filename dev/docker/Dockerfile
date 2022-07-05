FROM php:8.0-apache

RUN apt update && apt install -y \
  git \
  p7zip \
  coreutils \
  make \
  patch \
  unzip \
  zip \
  && rm -rf /var/lib/apt/lists/*

RUN pecl install ast \
  && docker-php-ext-enable ast

COPY --from=composer /usr/bin/composer /usr/bin/composer

# Allow override of the apache document root
ENV APACHE_DOCUMENT_ROOT /var/www/html

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite