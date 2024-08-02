#! /bin/bash

# Timestamp in YYYYMMDDHHMM format
TIMESTAMP=$(date +%Y%m%d%H%M)

# Filenames
BACKUP_FILE="/var/www/db-backups/crystelcare.sql"
COMPRESSED_FILE="/var/www/db-backups/crystelcare_${TIMESTAMP}.gz"

# Backup
mysqldump crystelcare > ${BACKUP_FILE}

# Compress
gzip -c ${BACKUP_FILE} > ${COMPRESSED_FILE}
