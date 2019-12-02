ARG phpImg=php:apache
FROM $phpImg
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
  && echo 'Listen ${APACHE_PORT}' > /etc/apache2/ports.conf \
  && echo 'ServerName grima' > /etc/apache2/sites-enabled/000-default.conf \
  && apt-get update && apt-get install -y libpq-dev \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && docker-php-ext-install pdo pdo_mysql pdo_pgsql \
  && rm -rf /var/lib/apt/lists/* && apt-get clean \
  && true
ENV apikey= server= APACHE_PORT=80 DATABASE_URL= SESSION_KEY= SESSION_MODULE= SESSION_NAME= SESSION_PATH=
COPY dist /var/www/html
