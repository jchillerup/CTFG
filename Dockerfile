FROM php:8.4-cli

COPY . /ctfg
WORKDIR /ctfg
RUN [ "/ctfg/install-composer.sh" ]
RUN [ "php", "composer.phar", "install" ]


CMD [ "php", "./your-script.php" ]
