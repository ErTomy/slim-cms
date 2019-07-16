FROM php:7.3-apache

COPY . /srv/app
COPY php/vhost.conf /etc/apache2/sites-available/000-default.conf
 
ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y --fix-missing \
    apt-utils \
    gnupg

RUN echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list
RUN echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list
RUN curl -sS --insecure https://www.dotdeb.org/dotdeb.gpg | apt-key add -

## ZIP

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev
RUN docker-php-ext-install zip

## GD

RUN apt-get update && \
    apt-get install -y libfreetype6-dev libjpeg62-turbo-dev && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install gd



RUN chown -R www-data:www-data /srv/app \
    && a2enmod rewrite headers && service apache2 restart