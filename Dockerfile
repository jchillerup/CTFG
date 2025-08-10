FROM php:8.4-cli

RUN apt-get update &&  DEBIAN_FRONTEND=noninteractive apt-get install --no-install-recommends --assume-yes \
    git

COPY . /ctfg
WORKDIR /ctfg
RUN [ "/ctfg/install-composer.sh" ]
RUN [ "php", "composer.phar", "install" ]

CMD [ "php", "./your-script.php" ]
