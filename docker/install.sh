#!/bin/bash
set -e

TARGET="/var/www"
REMOTE_REPOSITORY="https://github.com/gitonomy/gitonomy.git"
export HOME="/root"

echo "$PUBLIC_KEY" >> ~/.ssh/authorized_keys

if [ ! -f "/usr/bin/composer" ]; then
    echo ">> installing composer..."
    cd /tmp
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/bin/composer
else
    echo "-- composer installed"
fi


if [ ! -f "$TARGET/composer.json" ]; then
    echo ">> cloning sourcecode..."
    cd "$TARGET"
    git clone "$REMOTE_REPOSITORY" .
else
    echo "-- sourcecode cloned"
fi

if [ ! -d "$TARGET/vendor" ]; then
    echo ">> install dependencies..."
    cd "$TARGET"
    composer install
else
    echo "-- dependencies installed"
fi

cd "$TARGET"
./super-reset.sh

chown -R www-data: "$TARGET"
