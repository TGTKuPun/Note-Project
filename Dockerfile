FROM php:8.2-apache

# Cài đặt các extension cần thiết (mysqli, zip, opcache, pdo_mysql) và thư viện hỗ trợ
RUN docker-php-ext-install mysqli \
    && a2enmod rewrite

# Chuyển đến thư mục làm việc của container
WORKDIR /var/www/html/

# Sao chép mã nguồn frontend và backend vào container
# COPY ./frontend /var/www/html/
# COPY ./backend/api /var/www/html/api/

# Chạy Apache khi container được khởi động
CMD ["apache2-foreground"]
