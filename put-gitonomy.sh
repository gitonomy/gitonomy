#!/bin/bash
repository_path="`php app/console gitonomy:config:show repository_path`"

if [ -d "$repository_path/gitonomy.git" ]; then
    rm -Rf "$repository_path/gitonomy.git"
fi
mkdir -p "$repository_path"
git init --bare "$repository_path/gitonomy.git"
cp -r app/Resources/hooks "$repository_path/gitonomy.git"

git remote add __tmp__ "$repository_path/gitonomy.git"
git push __tmp__ master -q
git push --all __tmp__ -q
git remote rm __tmp__
