Installation
============

Gitonomy is still under development, and no distributed version is available by now.

Requirements
------------

To work properly, web-application needs to have full right on repositories. If you can,
run the same user for web-frontend and backend application.

If you can't, use group permissions to allow it to be possible.

Install dependencies
--------------------

Dependencies of the project are managed using Composer. This tool allows you to install
dependencies in two lines::

    curl -s https://getcomposer.org/installer | php
    php composer.phar install

Configuration
-------------

You can configure the application through web setup using online installation on
http://localhost/install.php

This step-by-step configuration will guide you through the setup process of Gitonomy.

The configuration of the application is located in ``app/config/parameters.yml``.
This file can be empty. All parameters will then be loaded from ``app/config/parameters_dist.yml``.
You can override any parameter from this distributed configuration file.

Database initialization
-----------------------

To start using the application, you need to load fixtures in it. First,
configure your application as explained above.

Create your MySQL database and when it's done, go to project and type::

    ./install.sh

This script will create database and load default data. On first time, connect with user **admin** (password: admin).
Go to your profile and change your password to something more obscure.

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
