#!/bin/bash

set -e
env=${APP_ENV:-local}

composer install --no-dev --no-interaction --no-autoloader --no-scripts
composer dump-autoload -o

## Done this here as it is depending on Redis for some stupid reason...
php artisan storage:link --env=prod

php artisan migrate --force

php artisan db:seed --force

if [ "$env" != "local" ]; then
    echo "Caching configuration..."
    php artisan optimize
else
    echo "Clear cache..."
    php artisan optimize:clear
fi

## Make sure to update the permissions once again (if vendor publish wrote some crap as root..)
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/public/storage
echo "Permissions updated on public storage folder!"

php artisan serve --host 0.0.0.0 &
php artisan kafka:consume &
while :
   do
     echo "Scheduler running..."
     php /var/www/html/artisan schedule:run --verbose --no-interaction &
     sleep 60
   done
