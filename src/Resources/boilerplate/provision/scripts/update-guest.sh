#!/usr/bin/env bash

add-apt-repository -y ppa:ondrej/php
add-apt-repository -y ppa:openjdk-r/ppa

apt-get update
apt-get upgrade -y
