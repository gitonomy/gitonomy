#!/bin/bash
set -e

if [ ! -d /gitonomy ]; then
    echo "/gitonomy not found"
fi

cd /gitonomy

echo "$PUBLIC_KEY" >> /root/.ssh/authorized_keys

USER_UID=${USER_UID:-1000}
USER_GID=${USER_GID:-1000}

# Set everything to the user now
if ! getent group gitonomy; then
    groupadd -f -g ${USER_UID} gitonomy
fi

if ! getent passwd gitonomy >/dev/null; then
    adduser --disabled-login --uid ${USER_UID} --gid ${USER_GID} --gecos 'gitonomy' gitonomy
fi

chown -R gitonomy:gitonomy /gitonomy

mysqld_safe &
echo "-- waiting for MySQL..."
while ! mysql -e "SHOW DATABASES" 1>/dev/null; do
    sleep 1
done

sudo -u gitonomy ./reset.sh
mysqladmin shutdown
