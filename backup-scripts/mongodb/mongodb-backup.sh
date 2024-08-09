#!/bin/bash

# Perform the MongoDB backup
mongodump --host localhost --username $MONGO_INITDB_ROOT_USERNAME --password $MONGO_INITDB_ROOT_PASSWORD --out /backup

# Sync the backup to S3
# aws s3 sync /data/dump s3://your-bucket-name/mongodb-backups
