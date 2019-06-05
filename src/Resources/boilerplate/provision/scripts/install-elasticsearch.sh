#!/usr/bin/env bash

wget https://download.elastic.co/elasticsearch/release/org/elasticsearch/distribution/deb/elasticsearch/2.4.2/elasticsearch-2.4.2.deb
dpkg -i elasticsearch-2.4.2.deb
rm -f elasticsearch-2.4.2.deb
