#!/bin/bash
if [ -d app/cache/repositories/symfony.git ]; then
    rm -Rf app/cache/repositories/symfony.git
fi
mkdir -p app/cache/repositories
git clone --bare vendor/symfony/symfony app/cache/repositories/symfony.git
./app/console gitonomy:project-create Symfony symfony
