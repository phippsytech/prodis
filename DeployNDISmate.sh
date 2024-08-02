#! /bin/bash

# CRM
cd /var/www/staging.phippsy.tech/ndismate/app
npm run build
cd /var/www/staging.phippsy.tech/ndismate/
php updateIndex.php
rsync -avz --delete ./app/dist/ phippsy@ndismate.com.au:/var/www/ndismate.app/public/

# SERVER
./DeployNDISmateServer.sh

curl -X POST -H "Content-Type: application/json" -d '{"action":"updateVersion"}' https://api.ndismate.app/App
