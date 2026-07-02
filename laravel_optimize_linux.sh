#!/bin/bash

echo "Running Laravel Optimization..."

php artisan storage:link
php artisan cache:clear
php artisan config:clear

php artisan optimize
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "Done!"
