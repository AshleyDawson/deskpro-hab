#!/usr/bin/env bash

sed -i 's/;date.timezone =/date.timezone = Europe\/London/g' /etc/php/7.1/cli/php.ini
sed -i 's/;date.timezone =/date.timezone = Europe\/London/g' /etc/php/7.1/fpm/php.ini
sed -i 's/memory_limit = 128M/memory_limit = 1024M/g' /etc/php/7.1/fpm/php.ini
sed -i 's/max_execution_time = 30/max_execution_time = 120/g' /etc/php/7.1/fpm/php.ini

service php7.1-fpm restart
