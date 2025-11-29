#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
	CREATE DATABASE $POSTGRES_DB;
	GRANT ALL PRIVILEGES ON DATABASE $POSTGRES_USER TO $POSTGRES_DB;

	CREATE EXTENSION postgis SCHEMA public;
  CREATE EXTENSION postgis_topology SCHEMA public;
  CREATE EXTENSION fuzzystrmatch SCHEMA public;
  CREATE EXTENSION postgis_tiger_geocoder SCHEMA public;
EOSQL
