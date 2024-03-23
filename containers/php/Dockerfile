# Defines the additional PHP extensions to be installed
ARG PHP_EXTENSIONS="pdo_pgsql"

# base image for all containers
FROM php:8.3.4-fpm-alpine as base
# install required packages
RUN apk add --no-cache netcat-openbsd
WORKDIR /var/www/html
# install required bins
COPY --from=mlocati/php-extension-installer:2.2.5 /usr/bin/install-php-extensions /usr/local/sbin/
COPY --from=composer:2.7.2 /usr/bin/composer /usr/local/bin/composer

# copy composer files and install dependencies
FROM base as vendor
COPY composer.json .
COPY composer.lock .
RUN --mount=type=cache,target=/root/.composer \
    composer install  --no-scripts --no-dev --ignore-platform-reqs

# cheat time by reusing previous layer and installing only dev dependencies
FROM vendor as vendor_dev
RUN --mount=type=cache,target=/root/.composer \
    composer i --no-scripts --ignore-platform-reqs

# copy application files
FROM base as application
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY storage ./storage
COPY artisan .
COPY composer.json .
COPY composer.lock .

# production image, with production configuration and application files
FROM base as production
# installing php extensions
ARG PHP_EXTENSIONS
RUN install-php-extensions ${PHP_EXTENSIONS} opcache
# configuring php
# PHP_INI_DIR is defined in the base image
COPY containers/php/conf.d "$PHP_INI_DIR/conf.d"
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "expose_php = off" >> "$PHP_INI_DIR/php.ini" \
    && echo "access.log = /dev/null" >> "/usr/local/etc/php-fpm.d/zz-docker.conf"
# adding container configuration
ADD --checksum=sha256:206a8f9b2177703fc5aa924d85ad6c72e82413e2d09635b4c9c82a1b65b5b3d5 \
    --chmod=777 \
    https://github.com/eficode/wait-for/releases/download/v2.2.4/wait-for /usr/local/bin/wait-for
COPY --chmod=777 containers/php/entrypoint.sh /usr/local/sbin/entrypoint
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
# add application files
COPY --from=application /var/www/html /var/www/html
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
# run composer platform check, dump and remove not needed executables
ARG COMPOSER_ALLOW_SUPERUSER=1
RUN composer check-platform-reqs \
    && composer dump-autoload \
    && rm /usr/local/bin/composer \
    && rm /usr/local/sbin/install-php-extensions
# copy correct acceelerated files in opcache configuration.
RUN echo -n "opcache.max_accelerated_files=" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo $(find . -name "*.php" | wc -l | awk '{print (int($1/1000)+2)*1000}') >> "$PHP_INI_DIR/conf.d/opcache.ini"
# add volumes needed to be shared thoughout the app containers
VOLUME ["/var/www/html/bootstrap", "/var/www/html/storage"]
HEALTHCHECK CMD php artisan health:check --no-notification --do-not-store-results --fail-command-on-failing-check

# dev base image, just installs xdebug and sets development configuration
FROM base as dev_common
ARG PHP_EXTENSIONS
RUN install-php-extensions ${PHP_EXTENSIONS} xdebug \
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
# adding container configuration
ADD --checksum=sha256:206a8f9b2177703fc5aa924d85ad6c72e82413e2d09635b4c9c82a1b65b5b3d5 \
    --chmod=777 \
    https://github.com/eficode/wait-for/releases/download/v2.2.4/wait-for /usr/local/bin/wait-for
COPY --chmod=777 containers/php/entrypoint.sh /usr/local/sbin/entrypoint
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

# this image will be used for development, adding development tools and setting up user
FROM dev_common as development
RUN apk add --no-cache \
        bash \
        git \
        postgresql15-client \
        shadow \
        sudo \
    && echo "access.log = /dev/null" >> "/usr/local/etc/php-fpm.d/zz-docker.conf" \
    && echo "ALL ALL=(ALL:ALL) NOPASSWD: ALL" > "/etc/sudoers.d/password_less_sudo"
# setup user, group and chmod entrypoint
ARG UID
ARG GID
RUN usermod -u "$UID" www-data \
    && groupmod -g "$GID" www-data
ENV XDEBUG_MODE=develop,debug,coverage
ENV XDEBUG_CONFIG="client_host=host.docker.internal"
USER www-data
HEALTHCHECK CMD php artisan health:check --no-notification --do-not-store-results --fail-command-on-failing-check

# this image will be used for testing, adding application files and simulating the production environment
FROM dev_common as testing
COPY --from=application --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=vendor_dev --chown=www-data:www-data /var/www/html/vendor /var/www/html/vendor
COPY --chown=www-data:www-data tests ./tests
COPY --chown=www-data:www-data phpunit.xml phpunit.xml
# dump autoloader
USER www-data
RUN composer check-platform-reqs \
    && composer dump-autoload
# run testing, caring for coverage mode and attention on user that the process is run on
ENV XDEBUG_MODE=coverage
CMD ["php", "artisan", "test", "--coverage-cobertura", "coverage.cobertura.xml"]
