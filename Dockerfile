FROM php:7.1.10-apache
COPY src/ /var/www/html
EXPOSE 80