#!/bin/bash
if [ ! -f "app/config/parameters.yml" ]; then
    echo "You must setup the parameters.yml file in app/config before running this file"
fi

php app/console doctrine:database:drop --force
php app/console doctrine:database:create

php app/console doctrine:schema:create
php app/console doctrine:fixtures:load

# Prepare repositories
rm -Rf app/cache/repositories
git clone --bare sample/foobar/.git app/cache/repositories/foobar.git
git clone --bare sample/barbaz/.git app/cache/repositories/barbaz.git

rm -Rf web/bundles
php app/console assets:install --symlink web
