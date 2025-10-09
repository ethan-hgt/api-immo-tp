FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    icu-dev \
    sqlite-libs \
    sqlite-dev \
    git \
    wget \
    bash

RUN docker-php-ext-install pdo pdo_sqlite intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN wget https://get.symfony.com/cli/installer -O installer.sh && \
    chmod +x installer.sh && \
    ./installer.sh && \
    rm installer.sh

    ENV PATH="/root/.symfony5/bin:${PATH}"

WORKDIR /var/www/html

CMD ["symfony", "local:server:start", "--no-tls", "--port=8000", "--allow-all-ip"]