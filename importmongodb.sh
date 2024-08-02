

# This gets run on the production side 
# mongodump --host='localhost' --port='27017' --db='crystelcare' --username='crystelcare' --password='zcd9YKC0fty_nmb6zun' --out='/srv/backup/crystelcare.mongodb'
# tar -czvf crystelcare.mongodb.tar.gz /srv/backup/crystelcare.mongodb

# download the mongodb backup
scp phippsy@ndismate.com.au:/srv/backup/crystelcare.mongodb.tar.gz .

# extract the file - this will extract into the crystelcare.mongodb
tar -xzvf crystelcare.mongodb.tar.gz --strip-components=2 -C ~/Projects/Prodis/

# import the mongodb
#docker exec -i mysql-client mysql --host="vultr-prod-c5961206-99f9-4086-b0e0-0dbf93fb01a5-vultr-prod-9a9e.vultrdb.com" --port=16751 --user="vultradmin" --password="AVNS_1Bq765WZHN1naTnvUpt" development < crystelcare.sql


docker exec -it mongo-prodis /bin/sh

docker exec -i mongo-prodis mongorestore --host localhost --port 27017 -u crystelcare -p zcd9YKC0fty_nmb6zun --authenticationDatabase admin --nsInclude=crystelcare.* /data/dump
