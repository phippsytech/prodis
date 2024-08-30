#!/bin/bash

PROJECT_DIR="/var/www/prodis"
SPECIFIC_FILE="/var/www/prodis/env.php"
WEBSOCKET_DIR="/var/www/prodis/src/WebsocketServer/"
WEBSOCKET_FILE="/var/www/prodis/servers/websocket/websocket.php"

API_SERVICE_NAME="api_server"
WEBSOCKET_SERVICE_NAME="websocket_server"
RESTART_DELAY=2  # Time in seconds to wait before restarting

last_restart_api=0
last_restart_websocket=0

restart_service() {
    local service_name=$1
    echo "Restarting ${service_name} due to changes..."
    supervisorctl restart "${service_name}"
    
    if [[ "${service_name}" == "${API_SERVICE_NAME}" ]]; then
        last_restart_api=$(date +%s)
    elif [[ "${service_name}" == "${WEBSOCKET_SERVICE_NAME}" ]]; then
        last_restart_websocket=$(date +%s)
    fi
}

inotifywait -m -r -e modify,attrib,move,create,delete,close_write --format '%w%f %e' \
    "${PROJECT_DIR}" "${SPECIFIC_FILE}" "${WEBSOCKET_FILE}" "${WEBSOCKET_DIR}" |
while read -r file event; do
    now=$(date +%s)
    
    if [[ "${file}" == "${WEBSOCKET_FILE}" || "${file}" == ${WEBSOCKET_DIR}* ]]; then
        if (( now - last_restart_websocket >= RESTART_DELAY )); then
            echo "Detected changes in ${file}: ${event}"
            restart_service "${WEBSOCKET_SERVICE_NAME}"
        else
            echo "Change detected for WebSocket service, but waiting for debounce delay..."
        fi
    else
        if (( now - last_restart_api >= RESTART_DELAY )); then
            echo "Detected changes in ${file}: ${event}"
            restart_service "${API_SERVICE_NAME}"
        else
            echo "Change detected for API service, but waiting for debounce delay..."
        fi
    fi
done
