FROM php:7.1.5-apache

MAINTAINER Majid Abdolhosseini <majid@mhndev.com>

RUN rm /var/lib/apt/lists/* -vrf && \
    apt-get clean && apt-get update

## -----------------------------------------------
## Install needed packages
## -----------------------------------------------

RUN apt-get update && apt-get install -y \
        wget \
        zlib1g-dev \
        libicu-dev \
        alien \
        libfreetype6-dev \
        libssl-dev \
        rlwrap \
        socat \
        && echo "upload_max_filesize = 8M" > /usr/local/etc/php/conf.d/upload.ini \
        && docker-php-ext-install zip

## -----------------------------------------------
## Install Composer inside container
## -----------------------------------------------

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

RUN a2enmod rewrite
RUN echo "\nServerName localhost\n" >> /etc/apache2/apache2.conf


## -----------------------------------------------
## Add needed commands inside container
## -----------------------------------------------

COPY ["./commands/*.sh", "/docker/bin/"]
COPY ["./app/", "/docker/app/"]

RUN chmod a+x /docker/bin/*.sh \
    && ln -s /docker/bin/feed.sh         /usr/local/bin/feed \
    && ln -s /docker/bin/excelToJson.sh  /usr/local/bin/excelToJson \
    && ln -s /docker/bin/bootup.sh       /usr/local/bin/bootup \
    && ln -s /docker/bin/update.sh       /usr/local/bin/update \
    && ln -s /docker/bin/entrypoint.sh   /usr/local/bin/entrypoint \
    && ln -s /docker/bin/run.sh          /usr/local/bin/run

## Cleanup
RUN apt-get purge -y \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/


ARG USER_ID
ARG USER_GROUP

ENV USER_ID=${USER_ID}
ENV USER_GROUP=${USER_GROUP}

EXPOSE 80

#ENTRYPOINT ["entrypoint"]

CMD ["run"]
