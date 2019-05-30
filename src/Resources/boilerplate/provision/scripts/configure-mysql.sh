#!/usr/bin/env bash

export MYSQL_PWD=root
mysql -uroot -e "CREATE USER 'root'@'%';"
mysql -uroot -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION; FLUSH PRIVILEGES;"
mysql -uroot -e "CREATE DATABASE deskpro;"
sed -i "s/bind-address/#bind-address/" /etc/mysql/mysql.conf.d/mysqld.cnf
service mysql restart
