FROM php:8.1-apache-buster

RUN apt-get update && apt-get install -y \
    git \
    libc6-dev \
    libsasl2-dev \
    libsasl2-modules \
    libssl-dev \
    zip \
    unzip \
    net-tools

RUN docker-php-ext-install pdo pdo_mysql exif sockets opcache

RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin --filename=composer

RUN git clone https://github.com/edenhill/librdkafka.git \
    && cd librdkafka \
    && ./configure \
    && make \
    && make install \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka

COPY docker/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-available/mpm_prefork.conf

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-dev --no-interaction --no-autoloader --no-scripts
COPY . ./
RUN composer dump-autoload --optimize

EXPOSE 8000

CMD apachectl -D FOREGROUND

RUN chmod +x ./docker/Docker.sh
ENTRYPOINT ["bash", "./docker/Docker.sh"]
