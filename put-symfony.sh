#!/bin/bash
./app/console gitonomy:project-create Symfony symfony
if [ -d app/cache/repositories/projects/symfony.git ]; then
    rm -Rf app/cache/repositories/projects/symfony.git
fi
mkdir -p app/cache/repositories/projects
git clone --bare vendor/symfony app/cache/repositories/projects/symfony.git
