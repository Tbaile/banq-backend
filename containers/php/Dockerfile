# image configuration
ARG PHP_EXTENSIONS="gd pdo_pgsql"
ARG ADDITIONAL_PACKAGES="postgresql-client"
ARG ADDITIONAL_DEV_PACKAGES=""

# base image for all containers
FROM php:8.4.12-fpm-alpine AS base
WORKDIR /var/www/html
# add binaries and container entrypoint
COPY --from=mlocati/php-extension-installer:2.9.7 /usr/bin/install-php-extensions /usr/local/sbin/
COPY --from=composer:2.8.11 /usr/bin/composer /usr/local/bin/composer
ADD --chmod=777 \
    --checksum=sha256:206a8f9b2177703fc5aa924d85ad6c72e82413e2d09635b4c9c82a1b65b5b3d5 \
    https://github.com/eficode/wait-for/releases/download/v2.2.4/wait-for /usr/local/bin/wait-for
COPY --chmod=777 containers/php/entrypoint.sh /usr/local/sbin/entrypoint
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

FROM base AS development
# install xdebug and additional packages
ARG PHP_EXTENSIONS
ARG ADDITIONAL_PACKAGES
ARG ADDITIONAL_DEV_PACKAGES
RUN apk add --no-cache \
        ${ADDITIONAL_PACKAGES} \
        ${ADDITIONAL_DEV_PACKAGES} \
        bash \
        git \
        shadow \
        sudo \
    && install-php-extensions \
        ${PHP_EXTENSIONS} \
        xdebug \
    # configuring php and passwordless sudo
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && echo "ALL ALL=(ALL:ALL) NOPASSWD: ALL" > "/etc/sudoers.d/password_less_sudo"
ARG UID
ARG GID
RUN usermod -u "$UID" www-data \
    && groupmod -g "$GID" www-data
ENV XDEBUG_MODE=develop,debug,coverage
ENV XDEBUG_CONFIG="client_host=host.docker.internal"
USER www-data

# vendor files
FROM base AS vendor
COPY composer.json .
COPY composer.lock .
RUN composer i --no-scripts --no-dev --ignore-platform-reqs

# vendor dev files
FROM vendor AS vendor_test
RUN composer i --no-scripts --ignore-platform-reqs

# all application files
FROM base AS application
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY --chown=www-data:www-data storage ./storage
COPY artisan .
COPY composer.json .
COPY composer.lock .

FROM base AS testing
ARG PHP_EXTENSIONS
ARG ADDITIONAL_PACKAGES
RUN apk add --no-cache ${ADDITIONAL_PACKAGES} \
    && install-php-extensions \
        ${PHP_EXTENSIONS} \
        xdebug
COPY --from=application /var/www/html /var/www/html
COPY --from=vendor_test /var/www/html/vendor /var/www/html/vendor
COPY tests ./tests
COPY phpunit.xml .
COPY .env.testing .env
RUN composer check-platform-reqs \
    && composer dump-autoload
USER www-data
ENV XDEBUG_MODE=coverage
CMD ["php", "artisan", "test"]

FROM base AS production
# container php extensions, packages and using official production configuration
ARG PHP_EXTENSIONS
ARG ADDITIONAL_PACKAGES
RUN apk add --no-cache ${ADDITIONAL_PACKAGES} \
    && install-php-extensions \
        ${PHP_EXTENSIONS} \
        opcache \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "* * * * * cd /var/www/html && php artisan schedule >> /dev/null 2>&1" > /etc/crontabs/www-data
# php prod configuration
COPY containers/php/conf.d /usr/local/etc/php/conf.d
# container ready, copy over application and do software bootstrap
COPY --from=application /var/www/html /var/www/html
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
ARG COMPOSER_ALLOW_SUPERUSER=1
RUN composer check-platform-reqs \
    && composer dump-autoload --optimize \
    && echo -n "opcache.max_accelerated_files=" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo $(find . -name "*.php" | wc -l | awk '{print (int($1/1000)+2)*1000}') >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && rm /usr/local/bin/composer \
    && rm /usr/local/sbin/install-php-extensions
VOLUME ["/var/www/html/storage"]
