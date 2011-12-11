#!/bin/bash
./reset.sh
./put-symfony.sh
./put-gitonomy.sh

./app/console gitonomy:ssh-key-create alice "Autokey" "`cat ~/.ssh/id_rsa.pub`"
./app/console gitonomy:authorized-keys -i | tee ~/.ssh/authorized_keys
