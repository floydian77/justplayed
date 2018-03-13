FROM nginx:1.13

ADD vhost.conf /etc/nginx/conf.d/default.conf
