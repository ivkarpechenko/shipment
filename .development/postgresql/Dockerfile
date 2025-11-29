FROM postgres:15

RUN localedef -i ru_RU -c -f UTF-8 -A /usr/share/locale/locale.alias ru_RU.UTF-8
ENV LANG ru_RU.utf8

RUN apt-get update; \
    apt-get install -yqq --no-install-recommends --show-progress \
          postgresql-15-postgis-3 \
          postgresql-15-postgis-3-scripts

RUN mkdir -p /docker-entrypoint-initdb.d
COPY docker-entrypoint-initdb.d/* /docker-entrypoint-initdb.d/