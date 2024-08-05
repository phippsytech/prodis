#!/bin/bash

# this brings redis, mongodb and rabbitmq up - disabled because it doesn't need to be restarted
#docker compose -f docker-compose.shared.yml up -d --build

# this brings the docker development system up
docker compose --env-file .env -f docker-compose.yml -f docker-compose.dev.yml up -d --build
