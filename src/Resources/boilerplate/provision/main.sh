#!/usr/bin/env bash

declare -a scripts=(
    "configure-guest"
    "update-guest"
    "install-utilities"
    "install-nginx"
    "configure-nginx"
    "install-php"
    "configure-php"
    "install-composer"
    "install-mysql"
    "configure-mysql"
    "install-node"
    "configure-node"
    "install-elasticsearch"
    "configure-elasticsearch"
    "configure-cron"
    "configure-deskpro"
)

for script in "${scripts[@]}"
do
   sudo bash "/var/www/deskpro/.hab/provision/scripts/$script.sh"
done
