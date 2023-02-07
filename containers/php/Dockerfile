FROM php:8.2.2-fpm-alpine as base
WORKDIR /var/www/html
COPY --from=mlocati/php-extension-installer:2.0.2 /usr/bin/install-php-extensions /usr/local/sbin/install-php-extensions
RUN install-php-extensions \
    pdo_pgsql \
    opcache \
    && apk add --no-cache \
    fcgi
COPY --from=composer:2.5.2 /usr/bin/composer /usr/local/bin/composer
ARG COMPOSER_ALLOW_SUPERUSER=1

FROM base as vendor
COPY composer.json .
COPY composer.lock .
RUN composer check-platform-reqs \
    && composer i --no-scripts --no-dev

FROM vendor as vendor_dev
RUN composer i --no-scripts

FROM base as application
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY lang ./lang
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY storage ./storage
COPY artisan .
COPY composer.json .
COPY composer.lock .

FROM base as testing
RUN install-php-extensions xdebug
COPY --from=application --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=vendor_dev --chown=www-data:www-data /var/www/html/vendor /var/www/html/vendor
COPY --chown=www-data:www-data tests ./tests
COPY --chown=www-data:www-data phpunit.xml phpunit.xml
RUN composer dump-autoload \
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
ENV XDEBUG_MODE=coverage
USER www-data
ENTRYPOINT [""]
CMD ["php", "artisan", "test", "--parallel", "--coverage-cobertura", "coverage.cobertura.xml"]

FROM base as production
COPY --from=application --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=vendor --chown=www-data:www-data /var/www/html/vendor /var/www/html/vendor
RUN composer dump-autoload \
    && rm /usr/local/bin/composer \
    && rm /usr/local/sbin/install-php-extensions
COPY containers/php/conf.d "$PHP_INI_DIR/conf.d"
COPY containers/php/entrypoint.sh /usr/local/bin/entrypoint
ADD https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/v0.5.0/php-fpm-healthcheck /usr/local/bin/
RUN chmod u+x /usr/local/bin/entrypoint \
    && chmod u+x /usr/local/bin/php-fpm-healthcheck \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "access.log = /dev/null" >> "/usr/local/etc/php-fpm.d/zz-docker.conf" \
    && echo "pm.status_path = /status" >> "/usr/local/etc/php-fpm.d/zz-docker.conf" \
    && echo -n "opcache.max_accelerated_files=" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo $(find . -name "*.php" | wc -l | awk '{print (int($1/1000)+2)*1000}') >> "$PHP_INI_DIR/conf.d/opcache.ini"
HEALTHCHECK CMD php-fpm-healthcheck
VOLUME ["/var/www/html/bootstrap", "/var/www/html/storage"]
ENTRYPOINT ["entrypoint"]
