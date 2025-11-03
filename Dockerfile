FROM ubuntu:22.04

# Install PHP 8.4 from Ondřej's PPA
RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    gnupg \
    && LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php \
    && apt-get update && apt-get install -y \
    php8.4-cli \
    php8.4-common \
    php8.4-mysql \
    php8.4-gd \
    php8.4-xml \
    php8.4-zip \
    php8.4-mbstring \
    php8.4-curl \
    git \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev

# Create PHP symlink (so 'php' command works)
RUN update-alternatives --set php /usr/bin/php8.4

# Increase PHP memory limit for Airtable sync operations
RUN echo "memory_limit = 1G" > /etc/php/8.4/cli/conf.d/memory-limit.ini

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
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "80"]