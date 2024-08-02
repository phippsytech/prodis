## Crystel Care CRM

This repository contains the files for building the CRM located at crm.crystelcare.com.au


## PHP-FPM Version update - I need to develop cleaner docs around these updates
sudo a2disconf php8.2-fpm
sudo a2enconf php8.3-fpm

Additionally, the proxy for fpm needs to be updated.
<FilesMatch "\.php$">
    SetHandler "proxy:unix:/var/run/php/php8.3-fpm.sock|fcgi://localhost/"
</FilesMatch>

sudo systemctl restart apache2
sudo systemctl restart php8.3-fpm

## Development environment
pm2 start "npm run dev" --name CrystelCareCRM

Mongodb SERVER

curl -fsSL https://pgp.mongodb.com/server-6.0.asc | \
   sudo gpg -o /usr/share/keyrings/mongodb-server-6.0.gpg \
   --dearmor

echo "deb [ arch=amd64,arm64 signed-by=/usr/share/keyrings/mongodb-server-6.0.gpg ] https://repo.mongodb.org/apt/ubuntu jammy/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-6.0.list

sudo apt-get install -y mongodb-org

sudo apt-get install -y mongodb-org-server


## install php libraries
sudo apt install php-mongodb
composer require mongodb/mongodb

some interesting notices...
NOTICE: Not enabling PHP 8.2 FPM by default.
NOTICE: To enable PHP 8.2 FPM in Apache2 do:
NOTICE: a2enmod proxy_fcgi setenvif
NOTICE: a2enconf php8.2-fpm
NOTICE: You are seeing this message because you have apache2 package installed.
Processing triggers for php8.2-cli (8.2.4-1+ubuntu22.04.1+deb.sury.org+1) ...
needrestart is being skipped since dpkg has failed


use admin
db.createUser(
  {
    user: xxx,
    pwd: xxx,
    roles: [ { role: "userAdminAnyDatabase", db: "admin" } ]
  }
)


use crystelcare
db.createUser(
  {
    user: xxx,
    pwd: xxx,
    roles: [ { role: "readWrite", db: "crystelcare" } ]
  }
)





## Offline

The app currently does a poor job if there is no internet connection.  It would
be good if the app could interpret if it is unable to connect to particular services
example xero.