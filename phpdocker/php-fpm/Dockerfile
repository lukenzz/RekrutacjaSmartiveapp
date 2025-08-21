FROM phpdockerio/php:8.4-fpm
WORKDIR "/app"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php8.4-intl \
        php8.4-cli \
        php8.4-gd \
        php8.4-tidy \
        php8.4-yaml \
        php8.4-mysql \
        php8.4-xml \
        php8.4-curl \
        php8.4-zip \
        php8.4-mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
