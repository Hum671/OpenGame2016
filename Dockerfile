FROM php:7.2-apache

# 安装 mod_rewrite 模块
RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev

COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# 安装 redis 和 mysqli 扩展
RUN pecl install redis redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable pdo_mysql \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ \
    && docker-php-ext-install -j "$(nproc)" gd

# 将项目目录复制到容器中
COPY . /var/www/html/

# 将Apache配置文件复制到容器中
COPY ./apache2.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html/runtime && \
    chmod -R 755 /var/www/html/runtime && \
    chown -R www-data:www-data /var/www/html/public/uploads && \
    chmod -R 755 /var/www/html/public/uploads

# 启动Apache服务
CMD ["apache2-foreground"]
