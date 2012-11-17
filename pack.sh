#!/bin/bash
set -e
cd "$(dirname "$0")"

git clean -f -d -x app web

echo ">> Prepare parameters.yml"
touch app/config/parameters.yml

if [ ! -f composer.phar ]; then
    echo ">> Download composer"
    curl -s http://getcomposer.org/installer | php
fi

echo ">> Install dependencies"
php composer.phar install --optimize-autoloader --prefer-dist

echo ">> Dump assets"
php app/console assetic:dump --env=prod --no-debug

echo ">> Remove development elements"
rm -rf app/cache/*
rm -rf app/logs/*

echo ">> Compress"
if [ -f pack.tar.gz ]; then
    rm pack.tar.gz
fi

ln -s . gitonomy
tar -czf pack.tar.gz --exclude=.git \
    gitonomy/app \
    gitonomy/src/Gitonomy/Bundle \
    gitonomy/src/Gitonomy/Component \
    gitonomy/web \
    gitonomy/vendor \
    gitonomy/install.sh \
    gitonomy/LICENSE \
    gitonomy/README.md

echo ">> Clean"
rm gitonomy
