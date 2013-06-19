#!/bin/bash
set -e

repository_path="`php app/console gitonomy:config:show repository_path`"

cd sample/barbaz

export GITONOMY_ENV="$env"
export GITONOMY_USER="alice"
export GITONOMY_PROJECT="barbaz"

git remote add __tmp__ "$repository_path/barbaz.git"
git push __tmp__ master:master -q
git remote rm __tmp__

cd ../..
