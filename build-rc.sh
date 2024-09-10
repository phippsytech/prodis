#!/bin/bash

# log into the vultr container registry
# docker login sjc.vultrcr.com/phippsytech

# NOTE: I've attempted to use credsStore to manage the login, but it doesn't seem to work.

# build the release candidate images.
#API
docker build -t sjc.vultrcr.com/phippsytech/prodis-api:release-candidate -f Dockerfile.api .
docker push sjc.vultrcr.com/phippsytech/prodis-api:release-candidate
#APP
docker build -t sjc.vultrcr.com/phippsytech/prodis-app:release-candidate -f Dockerfile.prod.app .
docker push sjc.vultrcr.com/phippsytech/prodis-app:release-candidate

