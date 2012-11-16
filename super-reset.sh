#!/bin/bash
set -e
cd `php -r "echo dirname(realpath('$0'));"`

./reset.sh

echo ">>> Creating user in Gitonomy"
./app/console gitonomy:user-create user user 'user@example.org' "User"
./app/console gitonomy:user-role-create user  "Project creator"

if [ -f ~/.ssh/id_rsa.pub ]; then
    echo ">>> Adding your SSH key in Gitonomy"
    ./app/console gitonomy:user-ssh-key-create user "SSH key from system" "`cat ~/.ssh/id_rsa.pub`"
fi

if [ -f ~/.ssh/authorized_keys -a ! -f ~/.ssh/authorized_keys.src ]; then
    echo ">>> Backing up your authorized_keys file in authorized_keys.src"
    cp ~/.ssh/authorized_keys ~/.ssh/authorized_keys.src
fi

if [ -f ~/.ssh/authorized_keys ]; then
    echo ">>> Replacing authorized_keys"
    ./app/console gitonomy:authorized-keys -i | tee ~/.ssh/authorized_keys
fi

if [ -f ~/.ssh/authorized_keys.src ]; then
    echo ">>> Appending authorized_keys.src"
    cat ~/.ssh/authorized_keys.src | tee -a ~/.ssh/authorized_keys
fi

./app/console gitonomy:project-create Symfony symfony
./app/console gitonomy:project-create Gitonomy gitonomy

for project in gitonomy symfony; do
    echo ">>> Adding user $USER as lead developer on project $project"
    ./app/console gitonomy:git-access create $project lead-dev "*" 1 1
    ./app/console gitonomy:user-role-create user  "Lead developer" $project
    ./app/console gitonomy:user-role-create admin "Lead developer" $project
done

export GITONOMY_USER="user"

echo ">>> Add Symfony project"
export GITONOMY_PROJECT="symfony"
./put-symfony.sh

echo ">>> Add Gitonomy project"
export GITONOMY_PROJECT="gitonomy"
./put-gitonomy.sh
