#!/bin/bash
./app/console gitonomy:project-create Gitonomy gitonomy
if [ -d app/cache/repositories/projects/gitonomy.git ]; then
    rm -Rf app/cache/repositories/projects/gitonomy.git
fi
mkdir -p app/cache/repositories/projects
cp -R .git app/cache/repositories/projects/gitonomy.git
