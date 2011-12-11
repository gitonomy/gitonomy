#!/bin/bash
./app/console gitonomy:project-create Gitonomy gitonomy
if [ -d app/cache/repositories/projects/gitonomy.git ]; then
    rm -Rf app/cache/repositories/projects/gitonomy.git
fi
mkdir -p app/cache/repositories/projects
git clone --bare . app/cache/repositories/projects/gitonomy.git
