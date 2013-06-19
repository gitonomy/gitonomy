How it works
============

Easy-to-setup
-------------

Gitonomy is a dead-simple PHP-MySQL application running git commands on your
repositories.

Application relies on MySQL and PHP 5.3. Repositories are accesible with SSH
or local access.

Flexible architecture
---------------------

Software is open-source so you can easily come up and hack software. You can
watch some events and make actions on your repositories.

Limitations
-----------

Gitonomy does not backup automatically your repositories. As default,
repositories are located in ``app/repositories``. When setting up your
Gitonomy platform, we recommend you to setup a backup on this folder, Gitonomy
won't do it automatically.

File permissions are also responsability of administrator: you need to make
sure Apache can read repositories and that CLI access has full write-access
to application cache.
