#!/bin/bash

# Navigate to the Laravel application directory
cd /var/www/html

# Start the queue worker for the default queue
/usr/bin/php artisan queue:work --sleep=0.01 &

# Start the Laravel Echo Server
/usr/local/bin/laravel-echo-server start &



 wait
