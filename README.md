- composer install
- sudo cp .env.example .env
- php artisan key:generate
- php artisan passport:keys
- php artisan passport:client --personal
- php artisan passport:client --password
-- copy client id and client secret to env file PASSPORT_PASSWORD_CLIENT_ID and PASSPORT_PASSWORD_CLIENT_SECRET
-- sudo chown www-data:www-data storage/oauth-private.key
-- sudo chown www-data:www-data storage/oauth-public.key
-- sudo chmod 600 storage/oauth-private.key
-- sudo chmod 600 storage/oauth-public.key

php artisan passport:keys --force
php artisan passport:client --personal
php artisan passport:client --password

** copy client id and client secret to env file PASSPORT_PASSWORD_CLIENT_ID and PASSPORT_PASSWORD_CLIENT_SECRET
**

sudo chown www-data:www-data storage/oauth-private.key
sudo chown www-data:www-data storage/oauth-public.key
sudo chmod 600 storage/oauth-private.key
sudo chmod 600 storage/oauth-public.key