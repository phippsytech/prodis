#!/bin/bash

# this brings the docker development system up
docker compose --env-file .env -f docker-compose.yml -f docker-compose.dev.yml up -d --build
