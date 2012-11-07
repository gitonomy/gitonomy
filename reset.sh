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

echo ">>> Cleanup repositories"
if [ -d app/cache/repositories ]; then
    rm -rf app/cache/repositories
fi
mkdir app/cache/repositories -p

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

echo ">>> Dropping database"
php app/console doctrine:database:drop --force --env=$env || true
echo ">>> Creating database"
php app/console doctrine:database:create --env=$env
echo ">>> Creating SQL schema"
php app/console doctrine:schema:create --env=$env
echo ">>> Loading fixtures in project"
php app/console doctrine:fixtures:load --append --env=$env

if [ ! -d "sample" ]; then
    echo ">>> Removing sample folder"
    rm sample -rf
fi

echo ">>> Uncompress sample/ folder"
tar -xzf sample.tar.gz sample

echo ">>> Recreating repository foobar"
cd sample/foobar
export GITONOMY_ENV="$env"
export GITONOMY_USER="alice"
export GITONOMY_PROJECT="foobar"
git remote add origin ../../app/cache/repositories/foobar.git
git push origin master:master -q
git push origin new-feature:new-feature -q
git push origin pagination:pagination -q
cd ../..

echo ">>> Recreating repository barbaz"
cd sample/barbaz
git remote add origin ../../app/cache/repositories/barbaz.git
export GITONOMY_ENV="$env"
export GITONOMY_USER="alice"
export GITONOMY_PROJECT="barbaz"
git push origin master:master -q
cd ../..

git init --bare  app/cache/repositories/empty.git -q
git init --bare  app/cache/repositories/secret.git -q

echo ">>> Installing assets"
rm -Rf web/bundles
php app/console assets:install --symlink web --env=$env
