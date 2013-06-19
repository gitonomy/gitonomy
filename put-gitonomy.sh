#!/bin/bash
set -e

repository_path="`php app/console gitonomy:config:show repository_path`"

git remote add __tmp__ "$repository_path/gitonomy.git"
git push __tmp__ master -q
git push --all __tmp__ -q
git remote rm __tmp__
