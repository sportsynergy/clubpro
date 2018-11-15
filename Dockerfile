FROM php:7.0.32-apache

RUN docker-php-ext-install mysqli && docker-php-ext-install pdo_mysql
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

RUN sed -i "s/short_open_tag = Off/short_open_tag = On/" /usr/local/etc/php/php.ini
