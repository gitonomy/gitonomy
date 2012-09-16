#!/bin/bash
set -e
cd `php -r "echo dirname(realpath('$0'));"`
if [ -z "$1" ]; then
  env="dev"
else
  env=$1
fi

echo "Environment: " $env
echo ""

if [ ! -f "app/config/parameters.yml" ]; then
    echo ">>> Touching app/config/parmeters.yml"
    touch app/config/parameters.yml
fi

if [ ! -f composer.phar ]; then
    echo ">>> Downloading composer.phar"
    curl -s http://getcomposer.org/installer | php
fi

if [ ! -d vendor ]; then
    echo ">>> Installing dependencies"
    php composer.phar install
fi

if [ ! -d "sample" ]; then
    echo ">>> Uncompress sample/ folder"
    tar -xzf sample.tar.gz sample
fi

echo ">>> Dropping database"
php app/console doctrine:database:drop --force --env=$env
echo ">>> Creating database"
php app/console doctrine:database:create --env=$env
echo ">>> Creating SQL schema"
php app/console doctrine:schema:create --env=$env
echo ">>> Loading fixtures in project"
php app/console doctrine:fixtures:load --append --env=$env

echo ">>> Recreating repositories folder (in app/cache/repositories)"
rm -Rf app/cache/repositories
git clone --bare sample/foobar/.git app/cache/repositories/foobar.git
git clone --bare sample/barbaz/.git app/cache/repositories/barbaz.git
git init --bare  app/cache/repositories/empty.git

echo ">>> Installing assets"
rm -Rf web/bundles
php app/console assets:install --symlink web --env=$env
