#!/bin/sh
set -e

mkdir -p /tmp/nginx_client_body
mkdir -p /tmp/nginx_proxy
mkdir -p /tmp/nginx_fastcgi

mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/logs
mkdir -p bootstrap/cache

php artisan storage:link
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

/usr/local/sbin/php-fpm -D
exec /usr/sbin/nginx -g "daemon off;"
