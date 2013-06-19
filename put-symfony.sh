#!/bin/bash
set -e
repository_path="`php app/console gitonomy:config:show repository_path`"

cd vendor/symfony/symfony
git remote add __tmp__ "$repository_path/symfony.git"
git push __tmp__ master -q
git push __tmp__ --tags -q
git remote rm __tmp__
cd ../../..
