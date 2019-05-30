#!/usr/bin/env bash

cat <<'END' > /etc/nginx/sites-available/default
server {
    server_name deskpro.local;
    listen 80;

    set $deskpro_www_root /var/www/deskpro/www;

    root $deskpro_www_root;

    proxy_buffer_size 128k;
    proxy_buffers 4 256k;
    proxy_busy_buffers_size 256k;

    fastcgi_buffer_size 128k;
    fastcgi_buffers 4 256k;
    fastcgi_busy_buffers_size 256k;

    location / {
        index index.php;
        try_files $uri /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        fastcgi_param SCRIPT_FILENAME $deskpro_www_root/index.php;
    }

    location ~ /assets/[a-zA-Z0-9_\-\.]+/(pub|web)/.*?$ {
        add_header 'Access-Control-Allow-Origin' '*';
        add_header 'Access-Control-Allow-Credentials' 'true';
        add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS';
        add_header 'Access-Control-Allow-Headers' 'DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';

        if ($request_method = 'OPTIONS') {
            add_header 'Access-Control-Max-Age' 1728000;
            add_header 'Content-Type' 'text/plain charset=UTF-8';
            add_header 'Content-Length' 0;
            return 204;
        }
    }

    error_log /var/log/nginx/deskpro.error.log;
    access_log /var/log/nginx/deskpro.access.log;
}
END

service nginx restart
service php7.1-fpm restart
