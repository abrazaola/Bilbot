FROM php:7.1.10-apache
RUN docker-php-ext-install pdo pdo_mysql
CMD php -f /var/www/html/init.php
EXPOSE 80