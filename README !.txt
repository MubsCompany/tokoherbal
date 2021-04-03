Jalankan perintah berikut di terminal folder project laravel

composer require --dev barryvdh/laravel-ide-helper
composer require --dev friendsofphp/php-cs-fixer
composer require --dev squizlabs/php_codesniffer


// Setelah PHP ARTISAN MIGRATE JALANKAN KODE DI BAWAH INI
php artisan ide-helper:generate
php artisan ide-helper:models