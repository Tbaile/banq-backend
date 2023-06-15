FROM nginx:1.25.1-alpine as production
WORKDIR /var/www/html
RUN apk add --no-cache netcat-openbsd
COPY containers/nginx/config /etc/nginx
ADD https://github.com/eficode/wait-for/releases/download/v2.2.4/wait-for /usr/local/bin/wait-for
COPY containers/nginx/entrypoint.sh /docker-entrypoint.d/90-wait-fpm.sh
RUN chmod u+x /usr/local/bin/wait-for \
    && chmod +x /docker-entrypoint.d/90-wait-fpm.sh
COPY public ./public
