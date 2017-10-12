FROM ubuntu:xenial

MAINTAINER Crosslend <dev@crosslend.com>

RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Berlin /etc/localtime

RUN apt-get clean && apt-get update

RUN apt-get install -y --force-yes apt-transport-https software-properties-common
RUN LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
RUN apt-get update

RUN apt-get install -y --force-yes \
git openssh-client curl awscli mysql-client php7.1 php7.1-cli php7.1-ftp php7.1-mcrypt php7.1-mbstring php7.1-xml php7.1-common \
php7.1-fpm php7.1-mysql php7.1-curl php7.1-zip php7.1-intl php7.1-json php-apcu php-geoip php-pear \
php-ssh2 php7.1-gd php7.1-readline php7.1-soap php7.1-xmlrpc php7.1-xsl php7.1-bcmath php-xdebug

# configure xdebug
RUN echo "error_reporting = E_ALL" >> /etc/php/7.1/mods-available/xdebug.ini
RUN echo "display_startup_errors = On" >> /etc/php/7.1/mods-available/xdebug.ini
RUN echo "display_errors = On" >> /etc/php/7.1/mods-available/xdebug.ini
RUN echo "xdebug.remote_autostart=1" >> /etc/php/7.1/mods-available/xdebug.ini
RUN echo "xdebug.remote_enable=1" >> /etc/php/7.1/mods-available/xdebug.ini
RUN echo "xdebug.remote_connect_back=0" >> /etc/php/7.1/mods-available/xdebug.ini
RUN echo "xdebug.profiler_enable=0" >> /etc/php/7.1/mods-available/xdebug.ini
RUN echo "xdebug.idekey=\"PHPSTORM\"" >> /etc/php/7.1/mods-available/xdebug.ini
RUN echo "xdebug.remote_host=10.0.2.2" >> /etc/php/7.1/mods-available/xdebug.ini

RUN curl https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod a+x /usr/bin/composer
RUN composer global require "hirak/prestissimo:^0.3"
RUN mkdir /root/.ssh
RUN ssh-keyscan github.com >> /root/.ssh/known_hosts

WORKDIR /var/www
