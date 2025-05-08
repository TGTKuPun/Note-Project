FROM php:8.2-apache

# Cài đặt các extension cần thiết (mysqli, zip, opcache, pdo_mysql) và thư viện hỗ trợ
RUN apt-get update && apt-get install -y libzip-dev \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-ext-install opcache \
    && docker-php-ext-install pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Chuyển đến thư mục làm việc của container
WORKDIR /var/www/html/

# Sao chép mã nguồn frontend và backend vào container
# COPY ./frontend /var/www/html/
# COPY ./backend/api /var/www/html/api/

# Cài thư viện PHP
RUN cd /var/www/html/api && composer install --no-interaction --prefer-dist --optimize-autoloader

# Chạy Apache khi container được khởi động
CMD ["apache2-foreground"]
