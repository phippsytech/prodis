#!/bin/bash

while true
do
    php /var/www/prodis/src/Models/Participant/ServiceAgreement/ExpiredServiceAgreementWorker.php
done

