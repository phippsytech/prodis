#!/bin/bash

PROJECT_DIR="/var/www/prodis"
SPECIFIC_FILE="/var/www/prodis/env.php"
SERVICE_NAME="api_server"
RESTART_DELAY=2  # Time in seconds to wait before restarting

last_restart=0

restart_service() {
    echo "Restarting ${SERVICE_NAME} due to changes..."
    supervisorctl restart "${SERVICE_NAME}"
    last_restart=$(date +%s)
}

inotifywait -m -r -e modify,attrib,move,create,delete,close_write --format '%w%f %e' "${PROJECT_DIR}" "${SPECIFIC_FILE}" |
while read -r file event; do
    now=$(date +%s)
    if (( now - last_restart >= RESTART_DELAY )); then
        echo "Detected changes in ${file}: ${event}"
        restart_service
    else
        echo "Change detected, but waiting for debounce delay..."
    fi
done