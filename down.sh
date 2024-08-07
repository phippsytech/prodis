#!/bin/bash

# this takes the docker development system down
docker compose -f docker-compose.yml -f docker-compose.dev.yml down
