  #! /bin/bash

# run production backup
# ssh root@www.bpmb.com.au "cd /var/www/db-backups/ && ./backup.sh"

# copy recent production backup to local
scp phippsy@ndismate.com.au:/srv/backup/crystelcare.sql .
# scp phippsy@ndismate.com.au:/srv/backup/crystelcare_20240515_143001.gz ./crystelcare.sql.gz

# gunzip ./crystelcare.sql.gz

# import production database into local database
#sudo mysql -u crystelcare -p crystelcare < crystelcare.sql

sed -i '/\/\*!999999\\- enable the sandbox mode \*\//d' crystelcare.sql
sed -i '/^USE `crystelcare`;/d' crystelcare.sql

# import production database into local database
docker exec -i mysql-client mysql --host="vultr-prod-c5961206-99f9-4086-b0e0-0dbf93fb01a5-vultr-prod-9a9e.vultrdb.com" --port=16751 --user="vultradmin" --password="AVNS_1Bq765WZHN1naTnvUpt" development < crystelcare.sql
    




    docker exec -it mysql-client mysql --host="vultr-prod-c5961206-99f9-4086-b0e0-0dbf93fb01a5-vultr-prod-9a9e.vultrdb.com" --port=16751 --user="vultradmin" --password="AVNS_1Bq765WZHN1naTnvUpt"