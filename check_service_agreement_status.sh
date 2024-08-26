#!/bin/bash


while true
do
    docker exec prodis-babasa-api php /var/www/prodis/src/Models/Participant/ServiceAgreement/WatchServiceAgreements.php produce
    sleep 86400  # Sleep for 24 hours
done
