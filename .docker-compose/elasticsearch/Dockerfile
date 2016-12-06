# --------------------------------------------------------------------
# | composer installed globally; available as "composer" command.
# |
# |
FROM payam/apache_php7

MAINTAINER Payam Naderi <naderi.payam@gmail.com>

# Install base packages
## install python-software-properties package on your system which
## provides add-apt-repository command then use the following set of
## commands to add PPA for PHP 7 in your Ubuntu system and install it
RUN rm /var/lib/apt/lists/* -vrf && \
    apt-get clean && apt-get update && \
    apt-get install -yq --fix-missing \
            python-software-properties \
	        software-properties-common && \
	add-apt-repository ppa:ondrej/php && \
	add-apt-repository ppa:ondrej/apache2

RUN apt-get install -yq --force-yes --fix-missing \
        alien \
        git \
        php7.0-dev \
	php7.0-mbstring \
        pkg-config libssl-dev libsslcommon2-dev \
        && git clone https://github.com/mongodb/mongo-php-driver.git \
        && cd mongo-php-driver \
        && git checkout tags/1.1.9 -b build \
        && git submodule sync && git submodule update --init \
        && phpize && ./configure && make all -j 5 && make install \
        && echo "extension=mongodb.so" >> /etc/php/7.0/apache2/php.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

## --------------------------------------------
## | Setup Poirot
## --------------------------------------------

COPY ["./*.sh", "/docker/"]
COPY ["./app/", "/docker/app/"]

RUN chmod a+x /docker/*.sh

## Cleanup
RUN apt-get purge -y \
    php7.0-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/

CMD ["/docker/run-override.sh"]
