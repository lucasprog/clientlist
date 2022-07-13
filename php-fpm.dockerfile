FROM php:7.4-fpm

# RUN apt-get update && apt-get install -y git libmcrypt-dev \
#     default-mysql-client libmagickwand-dev --no-install-recommends \

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libmcrypt-dev \
    zip \
    unzip \
    vim

RUN apt-get upgrade -y

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install mcrypt-1.0.4

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

RUN docker-php-ext-enable mcrypt

#Composer 2.2
# COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

#Composer Latest
COPY --from=composer /usr/bin/composer /usr/bin/composer

#RUN composer install
