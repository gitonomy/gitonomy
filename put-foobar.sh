#!/bin/bash
set -e

repository_path="`php app/console gitonomy:config:show repository_path`"

cd sample/foobar

export GITONOMY_ENV="$env"
export GITONOMY_USER="alice"
export GITONOMY_PROJECT="foobar"

git remote add __tmp__ "$repository_path/foobar.git"

git push __tmp__ master:master -q
git push __tmp__ new-feature:new-feature -q
git push __tmp__ pagination:pagination -q
git push __tmp__ master:to-delete -q

export GITONOMY_USER="lead"
git push __tmp__ :to-delete -q

git remote rm __tmp__

cd ../..
