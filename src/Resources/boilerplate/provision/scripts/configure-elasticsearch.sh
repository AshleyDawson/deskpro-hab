#!/usr/bin/env bash

sed -i "s/#network.host: 192.168.0.1/network.host: 0.0.0.0/" /etc/elasticsearch/elasticsearch.yml
sed -i "s/#http.port: 9200/http.port: 9200/" /etc/elasticsearch/elasticsearch.yml
