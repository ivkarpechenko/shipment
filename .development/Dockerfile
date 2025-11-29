# Accepted values: 8.2 - 8.1 - 8.0
ARG PHP_VERSION=8.2

###########################################

FROM php:${PHP_VERSION}-cli-buster

ARG WWWUSER=1000
ARG WWWGROUP=1000
ARG TZ=Europe/Moscow

ENV ROOT=/var/www
WORKDIR $ROOT

ENV DEBIAN_FRONTEND=noninteractive \
    TERM=xterm-color

SHELL ["/bin/bash", "-eou", "pipefail", "-c"]

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone

RUN apt-get update; \
    apt-get upgrade -yqq; \
    pecl -q channel-update pecl.php.net; \
    apt-get install -yqq --no-install-recommends --show-progress \
          apt-utils \
          gnupg \
          gosu \
          git \
          curl \
          wget \
          libcurl4-openssl-dev \
          ca-certificates \
          supervisor \
          libmemcached-dev \
          libz-dev \
          libbrotli-dev \
          libpq-dev \
          libjpeg-dev \
          libpng-dev \
          libfreetype6-dev \
          libssl-dev \
          libwebp-dev \
          libmcrypt-dev \
          libonig-dev \
          libzip-dev zip unzip \
          libargon2-1 \
          libidn2-0 \
          libpcre2-8-0 \
          libpcre3 \
          libxml2 \
          libzstd1 \
          procps \
          exiftool


###########################################
# sockets
###########################################

RUN docker-php-ext-install sockets;

###########################################
# zip
###########################################

RUN docker-php-ext-configure zip && docker-php-ext-install zip;

###########################################
# mbstring
###########################################

RUN docker-php-ext-install mbstring;

###########################################
# GD
###########################################

RUN docker-php-ext-configure gd \
            --prefix=/usr \
            --with-jpeg \
            --with-webp \
            --with-freetype \
    && docker-php-ext-install gd;

###########################################
# xDebug
###########################################

ARG INSTALL_XDEBUG=true

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
     pecl install xdebug \
     && docker-php-ext-enable xdebug; \
  fi

###########################################
# PHP Redis
###########################################

ARG INSTALL_PHPREDIS=false

RUN if [ ${INSTALL_PHPREDIS} = true ]; then \
      pecl -q install -o -f redis \
      && rm -rf /tmp/pear \
      && docker-php-ext-enable redis; \
  fi

###########################################
# PCNTL
###########################################

ARG INSTALL_PCNTL=true

RUN if [ ${INSTALL_PCNTL} = true ]; then \
      docker-php-ext-install pcntl; \
  fi

###########################################
# BCMath
###########################################

ARG INSTALL_BCMATH=true

RUN if [ ${INSTALL_BCMATH} = true ]; then \
      docker-php-ext-install bcmath; \
  fi

###########################################
# Exif
###########################################

ARG INSTALL_EXIF=true

RUN if [ ${INSTALL_EXIF} = true ]; then \
      docker-php-ext-configure exif \
      && docker-php-ext-install exif \
      && docker-php-ext-enable exif; \
  fi

###########################################
# RabbitMQ
###########################################

ARG INSTALL_RABBITMQ=false

RUN if [ ${INSTALL_RABBITMQ} = true ]; then \
      apt-get install -yqq --no-install-recommends --show-progress librabbitmq-dev \
      && pecl -q install -o -f amqp \
      && docker-php-ext-enable amqp; \
  fi

###########################################################################
# Human Language and Character Encoding Support
###########################################################################

ARG INSTALL_INTL=true

RUN if [ ${INSTALL_INTL} = true ]; then \
      apt-get install -yqq --no-install-recommends --show-progress zlib1g-dev libicu-dev g++ \
      && docker-php-ext-configure intl \
      && docker-php-ext-install intl; \
  fi

###########################################
# pdo_pgsql
###########################################

ARG INSTALL_PDO_PGSQL=true

RUN if [ ${INSTALL_PDO_PGSQL} = true ]; then \
      docker-php-ext-install pdo_pgsql; \
  fi

###########################################
# pgsql
###########################################

ARG INSTALL_PGSQL=true

RUN if [ ${INSTALL_PGSQL} = true ]; then \
      docker-php-ext-install pgsql; \
  fi

###########################################
# pgsql client and postgis
###########################################

ARG INSTALL_PG_CLIENT=true
ARG INSTALL_POSTGIS=true

RUN if [ ${INSTALL_PG_CLIENT} = true ]; then \
      . /etc/os-release \
      && echo "deb http://apt.postgresql.org/pub/repos/apt $VERSION_CODENAME-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
      && curl -sL https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - \
      && apt-get install -yqq --no-install-recommends --show-progress postgresql-client-15 postgis \
      && apt-get update; \
      if [ ${INSTALL_POSTGIS} = true ]; then \
        apt-get install -yqq --no-install-recommends --show-progress postgis; \
      fi; \
  fi


###########################################
# Composer
###########################################
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

###########################################

RUN groupadd --force -g $WWWGROUP app \
    && useradd -ms /bin/bash --no-log-init --no-user-group -g $WWWGROUP -u $WWWUSER app

RUN apt-get clean \
    && docker-php-source delete \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && rm /var/log/lastlog /var/log/faillog

COPY . ${ROOT}

COPY .development/supervisord* /etc/supervisor/conf.d/
COPY .development/conf.d/* /usr/local/etc/php/conf.d/

RUN chmod +x .development/entrypoint.sh
RUN cat .development/utilities.sh >> ~/.bashrc

EXPOSE 9501

ENTRYPOINT [".development/entrypoint.sh"]

HEALTHCHECK --start-period=5s --interval=60s --timeout=5s --retries=8 CMD curl --fail http://localhost:2114/health?plugin=http || exit 1

