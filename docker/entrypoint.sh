#!/bin/sh

echo "Caching configuration..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Caching views..."
php artisan view:cache

echo "Caching events..."
php artisan event:cache

echo "Starting FrankenPHP via Laravel Octane..."
exec php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=80 --admin-port=2019 --workers=auto
