#!/bin/bash

# Backup directory
BACKUP_DIR="/var/www/db-backups"

# Remove backup files older than 7 days
find ${BACKUP_DIR} -type f -name "crystelcare_*.gz" -mtime +7 -exec rm {} \;

echo "Backup cleanup complete"
