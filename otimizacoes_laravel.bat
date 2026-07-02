@echo off
echo ==========================================
echo      Running Laravel Optimization
echo ==========================================

echo.
echo Limpeza...
php artisan storage:link
php artisan config:clear
php artisan event:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear



echo.
echo Clearing and optimizing...
php artisan optimize
php artisan optimize:clear

echo.
echo Caching config...
php artisan config:cache

echo.
echo Caching events...
php artisan event:cache


echo.
echo Caching routes...
php artisan route:cache

echo.
echo Caching views...
php artisan view:cache

echo.
echo ==========================================
echo        All optimization done!
echo ==========================================


