#!/bin/bash

# log into the vultr container registry
# docker login sjc.vultrcr.com/phippsytech

# NOTE: I've attempted to use credsStore to manage the login, but it doesn't seem to work.

# Get the current date and time in the format YYYY-MM-DD-HH-MM
CURRENT_DATE=$(date +'%Y-%m-%d-%H-%M')

# this builds the production images.

docker build -t sjc.vultrcr.com/phippsytech/prodis-api:latest -f Dockerfile.api .
docker tag sjc.vultrcr.com/phippsytech/prodis-api:latest sjc.vultrcr.com/phippsytech/prodis-api:$CURRENT_DATE
docker push sjc.vultrcr.com/phippsytech/prodis-api:$CURRENT_DATE
docker push sjc.vultrcr.com/phippsytech/prodis-api:latest

docker build -t sjc.vultrcr.com/phippsytech/prodis-app:latest -f Dockerfile.prod.app .
docker tag sjc.vultrcr.com/phippsytech/prodis-app:latest sjc.vultrcr.com/phippsytech/prodis-app:$CURRENT_DATE
docker push sjc.vultrcr.com/phippsytech/prodis-app:$CURRENT_DATE
docker push sjc.vultrcr.com/phippsytech/prodis-app:latest
