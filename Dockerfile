FROM php:7.4-cli

RUN docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install \
     pcntl

RUN mkdir -p /usr/src/lead

WORKDIR /usr/src/lead

COPY . /usr/src/lead

CMD ["php", "./lead.php"]