FROM php:8.4.27-apache

RUN docker-php-ext-install mysqli && docker-php-ext-install pdo_mysql
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
COPY . /var/www/html

RUN sed -i "s/short_open_tag = Off/short_open_tag = On/" /usr/local/etc/php/php.ini
RUN sed -i 's/^Listen 80/Listen 8000/' /etc/apache2/ports.conf
RUN mkdir /var/log/sportsynergy && chown -R www-data:www-data /var/log/sportsynergy

# Install Composer
RUN  apt-get update -y && \
     apt-get upgrade -y && \
     apt-get dist-upgrade -y && \
     apt-get -y autoremove && \
     apt-get clean
     
RUN apt-get install -y zip awscli
RUN cp ./config/clubpro.logrotate /etc/logrotate.d/clubpro

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer