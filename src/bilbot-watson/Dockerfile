FROM php:5.6.12-fpm

RUN apt-get update \
    && curl -sL https://deb.nodesource.com/setup | bash - \
    && apt-get install -y git libssl-dev zlib1g-dev libicu-dev g++ \
    && docker-php-ext-install zip mbstring intl

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/bin/composer

ADD docker/php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www/docker-symfony
