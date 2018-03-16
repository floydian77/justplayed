FROM nginx:1.13

RUN apt-get update && apt-get install -y openssl \
    --no-install-recommends \
    && mkdir -p /etc/nginx/ssl \
    && openssl req -x509 -nodes -days 365 -newkey rsa:4096 \
    -subj "/C=NL/ST=Gelderland/L=Nijmegen/O=justplayed AG/OU=IT/CN=localhost" \
    -keyout /etc/nginx/ssl/nginx.key \
    -out /etc/nginx/ssl/nginx.crt

ADD vhost.conf /etc/nginx/conf.d/default.conf
