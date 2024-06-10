FROM php:8.3-fpm-alpine
# RUN docker-php-ext-install pdo_mysql
ADD install-composer.sh /var/www
WORKDIR /var/www
RUN chmod u+x install-composer.sh
RUN ./install-composer.sh
CMD ["php-fpm", "-F", "-R"]
EXPOSE 9000