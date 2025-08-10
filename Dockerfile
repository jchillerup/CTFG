FROM php:8.4-cli

RUN apt-get update &&  DEBIAN_FRONTEND=noninteractive apt-get install --no-install-recommends --assume-yes \
    git unzip

RUN mkdir /ctfg
COPY install-composer.sh /ctfg
WORKDIR /ctfg
RUN /ctfg/install-composer.sh


COPY . /ctfg
RUN [ "php", "composer.phar", "install" ]


EXPOSE 8000
CMD ["php", "artisan",  "serve", "--host",  "0.0.0.0", "--port", "8000"]


# COPY . /ctfg

# CMD [ "php", "./your-script.php" ]
