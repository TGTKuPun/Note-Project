FROM php:8.2-apache

RUN docker-php-ext-install mysqli

RUN a2enmod rewrite

WORKDIR /var/www/html/

COPY ./frontend /var/www/html/

COPY ./backend/api /var/www/html/api

CMD ["apache2-foreground"]
