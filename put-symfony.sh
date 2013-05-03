#!/bin/bash
repository_path="`php app/console gitonomy:config:show repository_path`"

if [ -d "$repository_path/symfony.git" ]; then
    rm -Rf "$repository_path/symfony.git"
fi
mkdir -p "$repository_path"
git init --bare "$repository_path/symfony.git"
cp -r app/Resources/hooks "$repository_path/symfony.git"

cd vendor/symfony/symfony
git remote add __tmp__ "$repository_path/symfony.git"
git push __tmp__ master -q
git push __tmp__ --tags -q
git remote rm __tmp__
cd ../../..
