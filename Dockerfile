FROM php:8.2

RUN apt-get update \
	&& apt-get install -y git unzip

RUN pecl install mongodb-1.16.2 \
    && docker-php-ext-enable mongodb

WORKDIR /var/www/html

# Install Composer & packages
COPY --from=composer /usr/bin/composer /usr/bin/composer
ARG COMPOSER_NO_INTERACTION=1
ARG COMPOSER_ALLOW_SUPERUSER=1
COPY ./composer.* .
RUN --mount=type=ssh composer install --no-scripts

# Copy code
COPY ./.env* .
COPY ./src ./src