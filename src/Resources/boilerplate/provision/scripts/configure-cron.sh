#!/usr/bin/env bash

(/usr/bin/crontab -u www-data -l ; /bin/echo "* * * * * /usr/bin/php /var/www/deskpro/app/run/targets/cron.php > /dev/null 2>&1") | /usr/bin/sort - | /usr/bin/uniq - | /usr/bin/crontab -u www-data -
