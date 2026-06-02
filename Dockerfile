# =========================================
# Stage 1: dipendenze PHP con Composer
# =========================================
FROM php:8.3-fpm-alpine AS composer_deps

RUN apk add --no-cache \
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

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-autoloader --optimize-autoloader --no-scripts

COPY . .

# =========================================
# Stage 2: compilazione asset con Node/Vite
# =========================================
FROM node:20-alpine AS assets

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY vite.config.* ./
COPY resources/ ./resources/
COPY public/ ./public/
COPY . .

# necessario perché theme.css importa da vendor/filament/...
COPY --from=composer_deps /app/vendor ./vendor

RUN npm run build

# =========================================
# Stage 3: immagine di produzione
# =========================================
FROM php:8.3-fpm-alpine AS production

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

RUN pecl install imagick \
    && docker-php-ext-enable imagick

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-autoloader --optimize-autoloader --no-scripts

COPY . .

COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-scripts

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

ENTRYPOINT ["/entrypoint.sh"]