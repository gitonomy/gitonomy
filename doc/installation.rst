Installation
============

Requirements
------------

To run properly, you need to use the same user for web-access and CLI commands.
You can access the 3 application by 3 different ways:

* CRON jobs (for authorized_keys generation)
* Git server access (through a SSH connection)
* Web access (application to browse and manage repositories)

All users using Gitonomy will be identified as the same user on the system.
Every user will have the same push URL (``ssh://user@example.org/foobar.git``).
In the application, Gitonomy accepts or deny access to the user, depending of
his credentials.

For this reason, we suggest you to use the same user for all operations, if you
are not a system guru.

Download and uncompress
-----------------------

Go to `download page <http://gitonomy.com/downloads>`_ and download latest
version from website.

Uncompress it to your prefered location, let's assume */var/www/gitonomy*.

If you are using Apache, configuration should be at least:

.. code-block::

    <VirtualHost *:80>
        ServerName git.example.org
        DocumentRoot /var/www/gitonomy/web
    </VirtualHost>

Make sure your *web/* folder is the only accessible folder through web.
Directory ``app/config`` contains sensitive data, it should not be accessible
with browser.

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
