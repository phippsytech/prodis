#!/bin/bash

# while true
# do
#     docker exec prodis-babasa-api php /var/www/prodis/src/Models/Participant/ServiceAgreement/WatchServiceAgreements.php consume
# done

docker exec prodis-babasa-api php /var/www/prodis/src/Models/Participant/ServiceAgreement/ExpiredServiceAgreementWorker.php