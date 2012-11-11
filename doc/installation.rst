Installation
============

Install last release
--------------------

To install project, download latest release and make your web server serve the
``web/`` folder.

**Step 1: download and uncompress**

To download Gitonomy, go to `download page <http://gitonomy.com/downloads>`_.
Select the last release, uncompress the archive in a folder accessible by your
web-server.

**Step 2: configure your webserver**

Configure your Nginx or Apache to serve web/ folder:

.. code-block:: text

    DocumentRoot /path/to/gitonomy/web

**Step 3: go to web-installation**

Open your browser and go to ``http://localhost/install.php``.

This page will guide you through the install process of Gitonomy.

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
