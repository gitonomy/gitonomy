Installation
============

Install last release
--------------------

To install project, download latest release and make your web server serve the
``web/`` folder.

**Step 1: download and uncompress**

To download Gitonomy, go to `download page <http://gitonomy.com/download>`_.
Select the last release, uncompress the archive in a folder accessible by your
web-server.

**Step 2: configure your webserver**

Configure your Nginx or Apache to serve web/ folder:

.. code-block:: text

    DocumentRoot /path/to/gitonomy/web

**Step 3: go to web-installation**

Open your browser and go to ``http://localhost/install.php``.

This page will guide you through the install process of Gitonomy.

**Step 4: setup database**

By now, database needs to be setup automatically. To create schema, use command:

.. code-block:: bash

    php app/console doctrine:schema:create

We recommend you to load default fixtures to get a usable stage of application:

    php app/console doctrine:fixtures:load

You can now login using ``admin:admin``.

Install development version
---------------------------

The code is hosted on github. To install it locally in one line, use:

.. code-block:: bash

    git clone git@github.com:gitonomy/gitonomy.git
    cd gitonomy
    ./super-reset.sh

This will setup the development version of the project, with demo accounts. It
will add demo repositories, too. You can connect using one of various
available accounts (see code).
