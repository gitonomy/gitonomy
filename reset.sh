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

echo ">>> Cleanup cache"
rm -rf app/cache/{dev,prod}

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

if [ -d "sample" ]; then
    echo ">>> Removing sample folder"
    rm sample -rf
fi

echo ">>> Create sample/ folder"
mkdir sample
git clone git://github.com/gitonomy/foobar.git sample/foobar --bare -q
git clone git://github.com/gitonomy/barbaz.git sample/barbaz --bare -q

echo ">>> Recreating repository foobar"
cd sample/foobar
export GITONOMY_ENV="$env"
export GITONOMY_USER="alice"
export GITONOMY_PROJECT="foobar"
git remote add __tmp__ ../../app/cache/repositories/foobar.git
git push __tmp__ master:master -q
git push __tmp__ master:master -q
git push __tmp__ new-feature:new-feature -q
git push __tmp__ pagination:pagination -q
git push __tmp__ master:to-delete -q
export GITONOMY_USER="lead"
git push __tmp__ :to-delete -q
git remote rm __tmp__
cd ../..

echo ">>> Recreating repository barbaz"
cd sample/barbaz
git remote add __tmp__ ../../app/cache/repositories/barbaz.git
export GITONOMY_ENV="$env"
export GITONOMY_USER="alice"
export GITONOMY_PROJECT="barbaz"
git push __tmp__ master:master -q
cd ../..

git init --bare  app/cache/repositories/empty.git -q
git init --bare  app/cache/repositories/secret.git -q

echo ">>> Installing assets"
rm -Rf web/bundles
php app/console assets:install --symlink web --env=$env

echo ">>> Trying to generate assets for production"
php app/console assetic:dump --env=prod --no-debug web || true
