FROM php:8.1-rc-cli

RUN apt-get update &&  apt-get install -y \
    zip \
    git \
    libicu-dev && \
    pecl install -f -o xdebug && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-install intl pcntl && \
    echo "xdebug.mode = develop,coverage,debug" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini" && \
    echo "xdebug.max_nesting_level = 1024" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini"


ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_PROCESS_TIMEOUT=1200

RUN curl --silent --show-error https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin --filename=composer
