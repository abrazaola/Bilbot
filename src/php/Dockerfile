FROM php:7.1.10-apache
RUN docker-php-ext-install pdo pdo_mysql
CMD while true; do /var/www/html/getUpdatesCLI.php; done
EXPOSE 80