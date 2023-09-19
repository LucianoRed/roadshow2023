FROM php:7.4.33-apache

RUN apt-get -y update && apt-get clean && \
    apt-get install -y vim \
    libonig-dev \
    libicu-dev \
    libzip-dev \
    libmemcached-dev \
    librdkafka-dev \
    libpng-dev \
    libjpeg-dev \
    git \
    rsyslog && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli && \
    docker-php-ext-enable mysqli && \
    docker-php-ext-install mbstring && \
    docker-php-ext-install exif && \
    docker-php-ext-install intl && \
    docker-php-ext-install zip && \
    docker-php-ext-install gd && \
    pecl install rdkafka && \
    pecl install memcached && \
    docker-php-ext-install -j$(nproc) iconv && \
    rm -r /tmp/* /var/cache/* && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-enable memcached
RUN docker-php-ext-enable gd
COPY DockerConfigFiles/apache2.conf /etc/apache2/apache2.conf
COPY DockerConfigFiles/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY DockerConfigFiles/ports.conf /etc/apache2/ports.conf
COPY DockerConfigFiles/php.ini /usr/local/etc/php/
RUN a2enmod remoteip && a2enmod headers
RUN chown 12345 /var/www/html && mkdir /logs && chmod 777 /logs 
ADD uploadapp/ /var/www/html/
RUN chmod -R 777 /var/www/html/uploads/ && chmod 777 /var/www/html/upload.php && chmod 777 /var/www/html/index.php

EXPOSE 8080

USER 12345
