#!/usr/bin/env bash
set -e

echo "===> Setting Nginx port to \$PORT=${PORT:-10000}"
sed -i "s/PORT_PLACEHOLDER/${PORT:-10000}/g" /etc/nginx/sites-available/default

echo "===> Caching Laravel config, routes, and views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "===> Running database migrations"
php artisan migrate --force

echo "===> Starting PHP-FPM"
php-fpm -D

echo "===> Starting Nginx"
nginx -g "daemon off;"