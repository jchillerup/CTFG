FROM php:8.4-cli

RUN apt-get update &&  DEBIAN_FRONTEND=noninteractive apt-get install --no-install-recommends --assume-yes \
    git unzip libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev

# Install MySQL PDO extension  
RUN docker-php-ext-install pdo pdo_mysql

# Install GD extension with support for JPEG, PNG, and WebP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd

# Increase PHP memory limit for Airtable sync operations
RUN echo "memory_limit = 1G" > /usr/local/etc/php/conf.d/memory-limit.ini

RUN mkdir /ctfg
COPY install-composer.sh /ctfg
WORKDIR /ctfg
RUN /ctfg/install-composer.sh


COPY . /ctfg
RUN [ "php", "composer.phar", "install" ]

# Create storage directories and set permissions
RUN mkdir -p /ctfg/storage/app/public/media && \
    chmod -R 755 /ctfg/storage && \
    php artisan storage:link


EXPOSE 80
CMD ["php", "artisan",  "serve", "--host",  "0.0.0.0", "--port", "80"]


# COPY . /ctfg

# CMD [ "php", "./your-script.php" ]
