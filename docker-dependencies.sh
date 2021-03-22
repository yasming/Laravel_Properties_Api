cp .env.example .env
composer install
composer global require laravel/installer
php artisan key:generate