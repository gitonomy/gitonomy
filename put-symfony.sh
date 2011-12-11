#!/bin/bash
./app/console gitonomy:project-create Symfony symfony
if [ -d app/cache/repositories/projects/symfony.git ]; then
    rm -Rf app/cache/repositories/projects/symfony.git
fi
mkdir -p app/cache/repositories/projects
cp -R vendor/symfony/.git app/cache/repositories/projects/symfony.git
