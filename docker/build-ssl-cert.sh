#!/bin/bash

openssl req -x509 -nodes -days 1 -newkey rsa:4096 \
    -subj "/C=NL/ST=Gelderland/L=Nijmegen/O=justplayed AG/OU=IT/CN=localhost" \
    -keyout ssl/nginx.key \
    -out ssl/nginx.crt