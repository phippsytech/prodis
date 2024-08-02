#! /bin/bash

# My
cd /var/www/staging.phippsy.tech/ndismate/my
npm run build
cd /var/www/staging.phippsy.tech/ndismate/
php updateMyIndex.php
rsync -avz --delete ./my/dist/ phippsy@ndismate.com.au:/var/www/my.ndismate.com.au/public/

# SERVER
./DeployNDISmateServer.sh