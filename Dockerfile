FROM php:8.0-apache

RUN apt-get update && \
    apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    libtesseract-dev \
    libleptonica-dev \
    tesseract-ocr-eng \
    tesseract-ocr-spa \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    nano 
    # Comentadas las líneas para habilitar SSL
    # a2enmod ssl && \
    # a2ensite default-ssl

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install zip pdo pdo_mysql gd

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./php20 /var/www/html

EXPOSE 80
