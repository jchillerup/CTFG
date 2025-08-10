FROM php:8.4-cli

COPY . /ctfg
WORKDIR /ctfg
RUN [ "/ctfg/install-composer.sh" ]
RUN [ "php", "composer.phar", "install" ]


RUN apt-get update &&  DEBIAN_FRONTEND=noninteractive apt-get install --no-install-recommends --assume-yes \
    git


CMD [ "php", "./your-script.php" ]
