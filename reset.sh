#!/bin/bash
set -e
cd `php -r "echo dirname(realpath('$0'));"`

if [ ! -f composer.phar ]; then
    echo "- download composer.phar"
    curl -s http://getcomposer.org/installer | php
fi

php composer.phar install

echo "- clean cache"
rm -rf app/cache/{dev,prod}

repository_path="`php app/console gitonomy:config:show repository_path`"

echo "- clean repositories"
if [ -d "$repository_path" ]; then
    rm -rf "$repository_path"
fi
mkdir -p "$repository_path"

echo "- drop database"
php app/console doctrine:database:drop --force -q || true
echo "- create database"
php app/console doctrine:database:create -q
echo "- create SQL schema"
php app/console doctrine:schema:create -q
echo "- load fixtures in project"
php app/console doctrine:fixtures:load -q --append

if [ ! -d "sample" ]; then
    echo "- recreate sample/ directory"
    mkdir sample
    git clone https://github.com/gitonomy/foobar.git sample/foobar --bare -q
    git clone https://github.com/gitonomy/barbaz.git sample/barbaz --bare -q
fi

echo "- recreate repository foobar"
./put-foobar.sh

echo "- recreate repository barbaz"
./put-barbaz.sh

echo "- install assets"
rm -Rf web/bundles
php app/console assets:install -q --symlink web

echo "- production assets"
php app/console assetic:dump --env=prod --no-debug web || true
