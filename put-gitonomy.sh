#!/bin/bash
if [ -d app/cache/repositories/gitonomy.git ]; then
    rm -Rf app/cache/repositories/gitonomy.git
fi
mkdir -p app/cache/repositories
git init --bare app/cache/repositories/gitonomy.git
cp -r app/Resources/hooks app/cache/repositories/gitonomy.git/

git remote add __tmp__ app/cache/repositories/gitonomy.git
git push __tmp__ master
git push --all __tmp__
git remote rm __tmp__

php app/console gitonomy:events:process
