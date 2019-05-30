#!/usr/bin/env bash

wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-6.4.0.deb
dpkg -i elasticsearch-6.4.0.deb
rm -f elasticsearch-6.4.0.deb
