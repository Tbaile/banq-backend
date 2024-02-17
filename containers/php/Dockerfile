FROM php:8.2.7-fpm-alpine as base
WORKDIR /var/www/html
# install required extensions and composer
COPY --from=mlocati/php-extension-installer:2.2.2 /usr/bin/install-php-extensions /usr/local/sbin/
RUN install-php-extensions pdo_pgsql
COPY --from=composer:2.7.1 /usr/bin/composer /usr/local/bin/composer
# avoids issue with composer runs as root
ARG COMPOSER_ALLOW_SUPERUSER=1

FROM base as vendor
# copy composer.json and check if we got platform reqs correctly
COPY composer.json .
COPY composer.lock .
RUN --mount=type=cache,target=/root/.composer \
    composer check-platform-reqs \
    && composer i --no-scripts --no-dev

FROM vendor as vendor_dev
# cheat time by reusing previous layer and installing only dev dependencies
RUN --mount=type=cache,target=/root/.composer \
    composer i --no-scripts

FROM base as application
# copy application
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

FROM base as dev_common
# install xdebug and configure development php.ini
RUN install-php-extensions xdebug \
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

FROM dev_common as development
# install additional software and echo configuration
RUN apk add --no-cache \
        bash \
        fcgi \
        git \
        netcat-openbsd \
        postgresql15-client \
        shadow \
        sudo \
    && echo "pm.status_path = /status" >> "/usr/local/etc/php-fpm.d/zz-docker.conf" \
    && echo "access.log = /dev/null" >> "/usr/local/etc/php-fpm.d/zz-docker.conf" \
    && echo "ALL ALL=(ALL:ALL) NOPASSWD: ALL" > "/etc/sudoers.d/password_less_sudo"
# copy entrypoint
ADD --checksum=sha256:8a457d0c027feab1c0fc2aae62c30133ab18a116a802649a711953cf66b31b57 \
    https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/v0.5.0/php-fpm-healthcheck /usr/local/bin/php-fpm-healthcheck
ADD --checksum=sha256:206a8f9b2177703fc5aa924d85ad6c72e82413e2d09635b4c9c82a1b65b5b3d5 \
    https://github.com/eficode/wait-for/releases/download/v2.2.4/wait-for /usr/local/bin/wait-for
COPY --link containers/php/entrypoint.sh /usr/local/bin/entrypoint
# setup user, group and chmod entrypoint
ARG UID
ARG GID
RUN usermod -u "$UID" www-data \
    && groupmod -g "$GID" www-data \
    && chmod 777 /usr/local/bin/entrypoint \
    && chmod 777 /usr/local/bin/php-fpm-healthcheck \
    && chmod 777 /usr/local/bin/wait-for
ENV XDEBUG_MODE=develop,debug,coverage
ENV XDEBUG_CONFIG="client_host=host.docker.internal"
USER www-data
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

FROM dev_common as testing
# copy application and testing files
COPY --from=application --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=vendor_dev --chown=www-data:www-data /var/www/html/vendor /var/www/html/vendor
COPY --chown=www-data:www-data tests ./tests
COPY --chown=www-data:www-data phpunit.xml phpunit.xml
# dump autoloader
RUN composer dump-autoload
# run testing, caring for coverage mode and attention on user that the process is run on
ENV XDEBUG_MODE=coverage
USER www-data
ENTRYPOINT [""]
CMD ["php", "artisan", "test", "--coverage-cobertura", "coverage.cobertura.xml"]

FROM base as production
# install production extensions and packages
RUN install-php-extensions opcache \
    && apk add --no-cache \
        fcgi \
        netcat-openbsd
ADD --checksum=sha256:8a457d0c027feab1c0fc2aae62c30133ab18a116a802649a711953cf66b31b57 \
    https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/v0.5.0/php-fpm-healthcheck /usr/local/sbin/php-fpm-healthcheck
ADD --checksum=sha256:206a8f9b2177703fc5aa924d85ad6c72e82413e2d09635b4c9c82a1b65b5b3d5 \
    https://github.com/eficode/wait-for/releases/download/v2.2.4/wait-for /usr/local/bin/wait-for
# additional configuration for container, adding healthcheck tool and entrypoint
COPY --link containers/php/conf.d "$PHP_INI_DIR/conf.d"
COPY --link containers/php/entrypoint.sh /usr/local/sbin/entrypoint
RUN chmod 700 /usr/local/sbin/entrypoint \
    && chmod 700 /usr/local/sbin/php-fpm-healthcheck \
    && chmod 777 /usr/local/bin/wait-for \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "expose_php = off" >> "$PHP_INI_DIR/php.ini" \
    && echo "access.log = /dev/null" >> "/usr/local/etc/php-fpm.d/zz-docker.conf" \
    && echo "pm.status_path = /status" >> "/usr/local/etc/php-fpm.d/zz-docker.conf" \
    && echo -n "opcache.max_accelerated_files=" >> "$PHP_INI_DIR/conf.d/opcache.ini"
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
# add application files
COPY --from=application /var/www/html /var/www/html
COPY --from=vendor /var/www/html/vendor /var/www/html/vendor
# run composer dump and remove not needed executables
RUN composer dump-autoload \
    && rm /usr/local/bin/composer \
    && rm /usr/local/sbin/install-php-extensions
# copy correct acceelerated files in opcache configuration.
RUN echo $(find . -name "*.php" | wc -l | awk '{print (int($1/1000)+2)*1000}') >> "$PHP_INI_DIR/conf.d/opcache.ini"
# add volumes needed to be shared thoughout the app containers
VOLUME ["/var/www/html/bootstrap", "/var/www/html/storage"]
