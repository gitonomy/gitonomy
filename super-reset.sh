#!/bin/bash
set -e
cd `php -r "echo dirname(realpath('$0'));"`

./reset.sh

echo "- create user in Gitonomy"
php app/console -q gitonomy:user-create user user 'user@example.org' "User"
php app/console -q gitonomy:user-role-create user  "Project creator"

if [ -f ~/.ssh/id_rsa.pub ]; then
    echo "- add your SSH key in Gitonomy"
    php app/console -q gitonomy:user-ssh-key-create user "SSH key from system" "`cat ~/.ssh/id_rsa.pub`"
fi

if [ -f ~/.ssh/authorized_keys -a ! -f ~/.ssh/authorized_keys.src ]; then
    echo "- backup up your authorized_keys file in authorized_keys.src"
    cp ~/.ssh/authorized_keys ~/.ssh/authorized_keys.src
fi

if [ -f ~/.ssh/authorized_keys ]; then
    echo "- replace authorized_keys"
    php app/console gitonomy:authorized-keys -i > ~/.ssh/authorized_keys
fi

if [ -f ~/.ssh/authorized_keys.src ]; then
    echo "- append authorized_keys.src"
    cat ~/.ssh/authorized_keys.src > ~/.ssh/authorized_keys
fi

echo "- create projects Symfony & Gitonomy"
php app/console -q gitonomy:project-create Symfony symfony
php app/console -q gitonomy:project-create Gitonomy gitonomy

for project in gitonomy symfony; do
    echo "- add user $USER as lead developer on project $project"
    php app/console -q gitonomy:user-role-create user  "Lead developer" $project
done

export GITONOMY_USER="user"

echo "- add Symfony project"
export GITONOMY_PROJECT="symfony"
./put-symfony.sh

echo "- add Gitonomy project"
export GITONOMY_PROJECT="gitonomy"
./put-gitonomy.sh
