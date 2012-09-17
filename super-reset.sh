#!/bin/bash
set -e
cd `php -r "echo dirname(realpath('$0'));"`

./reset.sh

echo ">>> Add Symfony project"
./put-symfony.sh

echo ">>> Add Symfony project"
./put-gitonomy.sh

echo ">>> Creating user in Gitonomy"
./app/console gitonomy:user-create user user 'user@example.org' "User"

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

for project in gitonomy symfony empty foobar; do
    echo ">>> Adding user $USER as lead developer on project $project"
    ./app/console gitonomy:user-role-create user  "Lead developer" $project
    ./app/console gitonomy:user-role-create admin "Lead developer" $project
done
