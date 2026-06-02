# =========================================
# Stage 1: compilazione asset con Node/Vite
# =========================================
FROM node:20-alpine AS assets

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY vite.config.* ./
COPY resources/ ./resources/
COPY public/ ./public/

# =========================================
# Stage 2: immagine di produzione
# =========================================
FROM php:8.3-fpm-alpine AS production

# Dipendenze di sistema
RUN apk add --no-cache \
    nginx \
    curl \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    imagemagick-dev \
    imagemagick \
    $PHPIZE_DEPS \
    pkgconfig \
    libc-dev

# Estensioni PHP
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        opcache \
        zip \
        bcmath \
        exif \
        intl \
        mbstring \
        gd

# Imagick
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Dipendenze PHP
COPY composer.json composer.lock ./
#RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader
RUN composer install --no-dev --no-autoloader --optimize-autoloader

# Copia il codice
COPY . .

RUN npm run build

# Asset compilati
COPY --from=assets /app/public/build ./public/build

# Finalizza Composer
RUN composer dump-autoload --optimize --no-scripts
    
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

ENTRYPOINT ["/entrypoint.sh"]