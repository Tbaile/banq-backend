FROM nginx:1.29.0-alpine AS production
WORKDIR /var/www/html/public
RUN apk add --no-cache netcat-openbsd
COPY containers/nginx/config /etc/nginx
ADD --checksum=sha256:206a8f9b2177703fc5aa924d85ad6c72e82413e2d09635b4c9c82a1b65b5b3d5 \
    https://github.com/eficode/wait-for/releases/download/v2.2.4/wait-for /usr/local/bin/wait-for
COPY containers/nginx/entrypoint.sh /docker-entrypoint.d/90-wait-fpm.sh
RUN chmod u+x /usr/local/bin/wait-for \
    && chmod +x /docker-entrypoint.d/90-wait-fpm.sh
COPY public /var/www/html/public
HEALTHCHECK CMD curl --fail --silent --output /dev/null http://localhost/status
