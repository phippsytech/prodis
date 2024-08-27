#!/bin/bash


while true
do
    php /var/www/prodis/src/Models/Participant/ServiceAgreement/WatchServiceAgreements.php produce
    sleep 86400  # Sleep for 24 hours
done
