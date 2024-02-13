FROM php:8.3-cli

RUN apt-get update &&  apt-get install -y \
      zip \
      git \
      wget \
      gpg \
      libicu-dev && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-install intl pcntl && \
    echo "xdebug.mode = develop,coverage,debug" >> "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini"

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_PROCESS_TIMEOUT=1200

RUN curl --silent --show-error https://getcomposer.org/installer | php -- \
      --install-dir=/usr/bin --filename=composer && \
    git config --global --add safe.directory "*"

RUN wget -O phive.phar https://phar.io/releases/phive.phar && \
    wget -O phive.phar.asc https://phar.io/releases/phive.phar.asc && \
    gpg --keyserver hkps://keys.openpgp.org --recv-keys 0x9D8A98B29B2D5D79 && \
    gpg --verify phive.phar.asc phive.phar && \
    chmod +x phive.phar && \
    mv phive.phar /usr/local/bin/phive \
