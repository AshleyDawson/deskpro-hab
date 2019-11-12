#!/usr/bin/env bash

echo "export PATH=/usr/local/lib/nodejs/node-v8.11.4-linux-x64/bin:\$PATH" >> /home/vagrant/.profile
ln -s /usr/local/lib/nodejs/node-v8.11.4-linux-x64/bin/node /usr/bin/node
ln -s /usr/local/lib/nodejs/node-v8.11.4-linux-x64/bin/npm /usr/bin/npm

npm cache clean --force

npm install -g \
    bower \
    gulp

cd /var/www/deskpro/www/assets/BUILD/pub
npm install \
    popper.js@^1.14.3 \
    angular@^1.2
cd ~/
