Installation
============

To install project, download latest release and make your web server serve the
``web/`` folder.

Make sure other folders (``app/`` especially) are not accessible. They contain
sensitive data.

Verify configuration from CLI
:::::::::::::::::::::::::::::

You can make sure of the system compatibility by running:

.. code-block:: bash

    $ php app/check.php

Next, you need to configure your application.

Install the fixture application in one-line
:::::::::::::::::::::::::::::::::::::::::::

.. code-block:: bash

    ./reset.sh

Install the demo application in one-line
::::::::::::::::::::::::::::::::::::::::

This script loads fixtures and extra projects. Whereas fixtures are meant to be
minimalist, this one will create a more-realistic set of data for a purpose of
demonstration.

.. code-block:: bash

    ./super-reset.sh

After this, load using ``user:user`` as credentials.
