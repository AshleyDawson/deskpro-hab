#!/usr/bin/env bash

cp -R /var/www/deskpro/dev/config_dev_new/* /var/www/deskpro/config/
sed -i "s/\$DB_CONFIG\['password'\] = '';/\$DB_CONFIG\['password'\] = 'root';/" /var/www/deskpro/config/config.database.php
sed -i "s/localhost\:9666/deskpro\.local\:9666/" /var/www/deskpro/config/config.paths.php
