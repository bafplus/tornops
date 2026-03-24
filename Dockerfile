FROM php:8.3-apache

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

RUN apt-get update && apt-get install -y \
    wget \
    gnupg2 \
    git \
    libzip-dev \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libcurl4-openssl-dev \
    libxml2-dev \
    libonig-dev \
    supervisor \
    cron \
    sudo \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        curl \
        xml \
        gd \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && a2enmod rewrite \
    && a2enmod headers \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y mariadb-server mariadb-client && apt-get clean

COPY docker/apache-site.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/mariadb.cnf /etc/mysql/mariadb.conf.d/99-tornops.cnf
COPY docker/init-db.sh /usr/local/bin/init-db.sh
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/init-db.sh /usr/local/bin/start.sh

RUN mkdir -p /run/mysqld /var/lib/mysql \
    && chown mysql:mysql /run/mysqld /var/lib/mysql

WORKDIR /var/www/html

EXPOSE 80 443

CMD ["/bin/bash", "/usr/local/bin/start.sh"]
