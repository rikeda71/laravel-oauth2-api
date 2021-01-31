FROM nginx:1.19-alpine

RUN apk update && apk upgrade \
    && apk --update add logrotate \
    && apk add --no-cache openssl bash \
    && touch /var/log/messages

ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_PORT=9000

# Create 'messages' file used from 'logrotate'

# Copy 'logrotate' config file
COPY logrotate/nginx /etc/logrotate.d/

USER nginx

EXPOSE 80 443
