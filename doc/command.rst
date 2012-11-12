Command-line interface
======================

**Create a user**

.. code-block:: bash

    $ php app/console gitonomy:user-create username password email fullname

**Create a project**

.. code-block:: bash

    $ php app/console gitonomy:project-create myproject "My project"

**Grant a user access to a project**

.. code-block:: bash

    $ php app/console gitonomy:user-role-create myuser "Lead developer" myproject
