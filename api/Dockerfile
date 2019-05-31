FROM php:7.2-cli

WORKDIR /srv/gridsound_api

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

CMD ["php", "-S", "0.0.0.0:80", "-t", "/srv/gridsound_api"]
