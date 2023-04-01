FROM nginx:1.23.4-alpine as production
WORKDIR /var/www/html
COPY containers/nginx/config /etc/nginx
COPY public ./public
HEALTHCHECK CMD curl --fail --silent --output /dev/null http://localhost/status
