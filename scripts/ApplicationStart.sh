#!/bin/bash
cd /var/www/html/
composer install -n
php artisan clear  
echo "Restarting Apache..." | sudo tee -a /var/log/deploy.log
sudo systemctl stop apache2
sudo systemctl start apache2
sudo systemctl restart apache2
sudo systemctl status apache2 | sudo tee -a /var/log/deploy.log
