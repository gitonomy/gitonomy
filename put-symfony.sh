#!/bin/bash
if [ -d app/cache/repositories/symfony.git ]; then
    rm -Rf app/cache/repositories/symfony.git
fi
mkdir -p app/cache/repositories
git init --bare app/cache/repositories/symfony.git
cp -r app/Resources/hooks app/cache/repositories/symfony.git/

cd vendor/symfony/symfony
git remote add __tmp__ ../../../app/cache/repositories/symfony.git
git push __tmp__ master -q
git push __tmp__ --tags -q
git remote rm __tmp__
cd ../../..
