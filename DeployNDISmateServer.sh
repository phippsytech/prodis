#! /bin/bash

# SERVER
rsync -avz --delete ./api/ phippsy@ndismate.com.au:/var/www/ndismate.app/api/
rsync -avz --delete ./src/ phippsy@ndismate.com.au:/var/www/ndismate.app/src/
rsync -avz --delete ./servers/ phippsy@ndismate.com.au:/var/www/ndismate.app/servers/
rsync -avz --delete ./templates/ phippsy@ndismate.com.au:/var/www/ndismate.app/templates/
rsync -avz --delete ./qr/ phippsy@ndismate.com.au:/var/www/ndismate.app/qr/
rsync -avz --delete ./webhooks/ phippsy@ndismate.com.au:/var/www/ndismate.app/webhooks/
rsync -avz ./init.php phippsy@ndismate.com.au:/var/www/ndismate.app/init.php
rsync -avz ./composer.json phippsy@ndismate.com.au:/var/www/ndismate.app/composer.json
rsync -avz ./composer.lock phippsy@ndismate.com.au:/var/www/ndismate.app/composer.lock
rsync -avz ./autoload.php phippsy@ndismate.com.au:/var/www/ndismate.app/autoload.php