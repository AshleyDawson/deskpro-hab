#!/usr/bin/env bash

echo "export PATH=/usr/local/lib/nodejs/node-v10.12.0-linux-x64/bin:\$PATH" >> /home/vagrant/.profile
ln -s /usr/local/lib/nodejs/node-v10.12.0-linux-x64/bin/node /usr/bin/node
ln -s /usr/local/lib/nodejs/node-v10.12.0-linux-x64/bin/npm /usr/bin/npm

npm install -g \
    bower \
    gulp \
    cpx

chown -R vagrant:vagrant /home/vagrant/.npm
chown -R vagrant:vagrant /home/vagrant/.config
