# This gets run on the production side 
# mongodump --host='localhost' --port='27017' --db='crystelcare' --username='crystelcare' --password='zcd9YKC0fty_nmb6zun' --out='/srv/backup/crystelcare.mongodb'
# tar -czvf crystelcare.mongodb.tar.gz /srv/backup/crystelcare.mongodb

# NOTE: the container needs to be running before you can import the database!
# ensure that you create the volume
# docker volume create mongo-data

# NOTE: the data directory needs to have the right permission
# chmod -R 755 data

# download the mongodb backup
#scp phippsy@ndismate.com.au:/srv/backup/crystelcare.mongodb.tar.gz .

# extract the file - this will extract into the crystelcare.mongodb
tar --no-same-owner -xzvf crystelcare.mongodb.tar.gz --strip-components=2 -C ~/Projects/Prodis/data/mongodb/

# import the mongodb
docker exec -i mongo-prodis mongorestore --host localhost --port 27017 -u crystelcare -p zcd9YKC0fty_nmb6zun --authenticationDatabase admin --nsInclude=crystelcare.* /data/dump

# rm crystelcare.mongodb.tar.gz
# cm crystelcare.mongodb