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
        && echo "upload_max_filesize = 8M" > /usr/local/etc/php/conf.d/upload.ini

## -----------------------------------------------
## Install Composer inside container
## -----------------------------------------------

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

RUN a2enmod rewrite
RUN echo "\nServerName localhost\n" >> /etc/apache2/apache2.conf


## -----------------------------------------------
## Install Gosu grab gosu for easy step-down from root
## -----------------------------------------------

# grab gosu for easy step-down from root
ENV GOSU_VERSION 1.10
RUN set -x \
	&& wget -O /usr/local/bin/gosu "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$(dpkg --print-architecture)" \
	&& wget -O /usr/local/bin/gosu.asc "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$(dpkg --print-architecture).asc" \
	&& export GNUPGHOME="$(mktemp -d)" \
	&& gpg --keyserver ha.pool.sks-keyservers.net --recv-keys B42F6819007F00F88E364FD4036A9C25BF357DD4 \
	&& gpg --batch --verify /usr/local/bin/gosu.asc /usr/local/bin/gosu \
	&& rm -r "$GNUPGHOME" /usr/local/bin/gosu.asc \
	&& chmod +x /usr/local/bin/gosu \
&& gosu nobody true


## -----------------------------------------------
## Add needed commands inside container
## -----------------------------------------------

COPY ["./commands/*.sh", "/docker/bin/"]
COPY ["./app/", "/docker/app/"]

RUN chmod a+x /docker/bin/*.sh \
    && ln -s /docker/bin/feed.sh /usr/local/bin/feed \
    && ln -s /docker/bin/bootup.sh /usr/local/bin/bootup \
    && ln -s /docker/bin/update.sh /usr/local/bin/update \
    && ln -s /docker/bin/entrypoint.sh /usr/local/bin/entrypoint \
    && ln -s /docker/bin/run-override.sh /usr/local/bin/run-override

## Cleanup
RUN apt-get purge -y \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/


ARG USER_ID
ARG USER_GROUP

ENV USER_ID=${USER_ID}
ENV USER_GROUP=${USER_GROUP}

RUN chmod -R a+x /var/log/apache2

EXPOSE 80

#ENTRYPOINT ["entrypoint"]

CMD ["run-override"]
