#!/bin/bash
./app/console gitonomy:project-create Gitonomy gitonomy
if [ -d app/cache/repositories/gitonomy.git ]; then
    rm -Rf app/cache/repositories/gitonomy.git
fi
mkdir -p app/cache/repositories
git clone --bare . app/cache/repositories/gitonomy.git
