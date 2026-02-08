FROM php:8.5-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    librabbitmq-dev \
    libssl-dev \
    supervisor \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd \
    && pecl install xdebug amqp \
    && docker-php-ext-enable xdebug amqp \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/marketplace-profile

COPY docker/php/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/conf/php.ini /usr/local/etc/php/php.ini
COPY docker/php/conf/fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/conf/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --chown=www-data:www-data composer.json composer.lock symfony.lock ./
RUN composer install --no-autoloader --no-scripts

COPY --chown=www-data:www-data . .

RUN composer dump-autoload --optimize && \
    composer run-script post-install-cmd

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
