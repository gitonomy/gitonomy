#!/bin/bash
if [ ! -f "app/config/parameters.yml" ]; then
    echo "You must setup the parameters.yml file in app/config before running this file"
fi

if [ -z "$1" ]
then
  env="dev"
else
  env=$1
fi

if [ ! -d "sample" ]; then
    tar -xzf sample.tar.gz sample
fi

php app/console doctrine:database:drop --force --env=$env
php app/console doctrine:database:create --env=$env

php app/console doctrine:schema:create --env=$env
php app/console doctrine:fixtures:load --append --env=$env

# Prepare repositories
rm -Rf app/cache/repositories
git clone --bare sample/foobar/.git app/cache/repositories/foobar.git
git clone --bare sample/barbaz/.git app/cache/repositories/barbaz.git
git init --bare  app/cache/repositories/empty.git

rm -Rf web/bundles
php app/console assets:install --symlink web --env=$env
