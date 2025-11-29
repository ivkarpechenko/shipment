#!/usr/bin/env bash
set -e

php() {
  su app -c "php $*"
}

php_sudo() {
  su root -c "php $*"
}

initialStuff() {
  composer install
  vendor/bin/rr get --location bin/
  chmod +x /var/www/bin/rr
  php /var/www/bin/console doctrine:cache:clear-metadata --env=dev
  php /var/www/bin/console doctrine:migrations:migrate -n --env=dev
  php /var/www/bin/console cache:clear --env=dev
}

initialStuff
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.app.conf
