FROM php:8.3-alpine AS base-image
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

FROM base-image AS dev-stage
RUN apk add --no-cache postgresql-dev libpq
RUN docker-php-ext-install pdo pdo_pgsql
RUN docker-php-ext-enable apcu \
    && echo "apc.enable_cli=1" > /usr/local/etc/php/conf.d/apcu.ini
ENV docker=true
EXPOSE 8080
COPY . /var/www
COPY ./logs /var/www/logs
WORKDIR /var/www
CMD php -S 0.0.0.0:8080 -t public

