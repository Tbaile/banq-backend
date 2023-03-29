FROM nginx:1.23.4-alpine as production
WORKDIR /var/www/html
RUN apk add --no-cache netcat-openbsd
COPY containers/nginx/config /etc/nginx
ADD https://github.com/eficode/wait-for/releases/download/v2.2.4/wait-for /usr/local/bin/wait-for
COPY containers/nginx/entrypoint.sh /entrypoint
RUN chmod u+x /entrypoint \
    && chmod +x /usr/local/bin/wait-for
ENTRYPOINT ["/entrypoint"]
CMD ["nginx", "-g", "daemon off;"]
COPY public ./public
