FROM php:8.3-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    mariadb-server \
    redis-server \
    gettext-base \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Install Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

# Enable Apache modules
RUN a2enmod rewrite

# Copy entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy Apache config template (we renamed the local one to .template inside the container)
COPY apache-site.conf /etc/apache2/sites-available/000-default.conf.template

# Expose ports
EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]
