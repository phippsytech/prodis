#!/bin/bash

# This file is managed with supervisor now.


# Path to your project directory
PROJECT_DIR="/var/www/prodis"

# Path to the specific file
SPECIFIC_FILE="/var/www/prodis/env.php"


# Service name
SERVICE_NAME="api_server"

#action to restart the service
restart_service() {
    echo "Restarting ${SERVICE_NAME} due to changes in ${SPECIFIC_FILE}..."
    supervisorctl restart "${SERVICE_NAME}"
}

# Combined watch on the project directory and specific file
inotifywait -m -r -e modify,attrib,move,create,delete,close_write --format '%w%f %e' "${PROJECT_DIR}" "${SPECIFIC_FILE}" |
while read -r file event; do
    echo "Detected changes in ${file}: ${event}"
    restart_service
done