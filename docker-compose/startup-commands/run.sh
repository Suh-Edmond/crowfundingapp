#!/bin/sh

cd /var/www

# clear cache
php artisan cache:clear

# clear route cache
php artisan route:cache

#Generate app key
php artisan key:generate

# Optimizing Configuration loading
php artisan config:cache

# Optimizing Route loading
php artisan route:cache

# Optimizing View loading
php artisan view:cache
echo "finished cashes"

# Run migrations
php artisan migrate --force

#RUN seeders
php artisan db:seed

echo "Completed app startup"

exec "$@"
