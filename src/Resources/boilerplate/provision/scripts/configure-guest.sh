#!/usr/bin/env bash

timedatectl set-timezone Europe/London
export DEBIAN_FRONTEND="noninteractive"

cat <<'END' >> /home/vagrant/.bashrc
echo "______          _                    "
echo "|  _  \        | |                   "
echo "| | | |___  ___| | ___ __  _ __ ___  "
echo "| | | / _ \/ __| |/ / '_ \| '__/ _ \ "
echo "| |/ /  __/\__ \   <| |_) | | | (_) |"
echo "|___/ \___||___/_|\_\ .__/|_|  \___/ "
echo "                    | |              "
echo "                    |_|              "
echo ""
cd /var/www/deskpro
END
