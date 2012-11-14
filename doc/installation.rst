Installation
============

Gitonomy is still under development, and no distributed version is available by now.

Manual installation
-------------------

**Add CRON job**

Edit your crontab and add::

    * * * * * php /path/to/gitonomy/app/console gitonomy:authorized-keys -i > ~/.ssh/authorized_keys

Install development version
---------------------------

The code is hosted on github. To clone it locally:

.. code-block:: bash

    git clone git@github.com:gitonomy/gitonomy.git
    cd gitonomy
    ./super-reset.sh

This will setup the development version of the project, with demo accounts. It
will add demo repositories, too. You can connect using one of various
available accounts:

* admin:admin
* user:user
* alice:alice
* visitor:visitor
