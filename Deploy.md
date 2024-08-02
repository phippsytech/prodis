## Deploying the Crystel Care CRM

### Overview
The Crystel Care CRM is a client server app that needs to be built to be deployed on the production server.

## Production Server Structure:

/var/www/vhosts/crm.crystelcare.com.au
    /public <-- this is where we put the built Client App for the CRM
    /log <-- apache log files
    /src <-- server files
    /api <-- api endpoints that are called by the client app

    we also need 
    /composer.json
    /composer.lock
    /env.php <-- NOT in source control to protect secrets!
    /init.php

## Deploying Server
copy the following files to the server:
/src
/api
/init.php
/composer.json
/composer.lock
/autoload.php <-- this lets us place class names in folders eg: src/Class/Thing/Thing.php becomes Namespace\Class\Thing

## SSH 
First we need to make sure the server can login without needing a password prompt.  
We can do that using ssh-copy-id user@server.address

Note, when using rsync - the directories must exist in the target server.

Once you've set it up, you should be able to rsync in

rsync -avz --delete ./api/ root@www.bpmb.com.au:/var/www/vhosts/test/api/

rsync -avz --delete ./src/ user@remote-server:/var/www/vhosts/crm.crystelcare.com.au/src/
rsync -avz --delete ./api/ user@remote-server:/var/www/vhosts/crm.crystelcare.com.au/api/


rsync -avz ./init.php user@remote-server:/var/www/vhosts/crm.crystelcare.com.au/init.php
rsync -avz ./composer.json user@remote-server:/var/www/vhosts/crm.crystelcare.com.au/composer.json
rsync -avz ./composer.lock user@remote-server:/var/www/vhosts/crm.crystelcare.com.au/composer.lock

ssh user@www.bpmb.com.au "cd /var/www/vhosts/crm.crystelcare.com.au/ && composer install"

(and make sure env.php is set up correctly)

from the /var/www/vhosts/crm.crystelcare.com.au directory run `composer install`

That should make the server ready for action.

## Deploying Client
from the Build environment:
change to the webapp directory
(crm.crystelcare.com.au)/webapp/
npm run build

on the production server
copy the contents of (crm.crystelcare.com.au)/webapp/dist to /var/www/vhosts/crm.crystelcare.com.au/public


rsync -avz --delete ./webapp/dist/ user@remote-server:/var/www/vhosts/crm.crystelcare.com.au/public/


The app should be good to go