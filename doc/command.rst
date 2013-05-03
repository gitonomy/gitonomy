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

**Configuration of Gitonomy**

If you need to read configuration from Gitonomy, please use the ``gitonomy:config:show``
command:

.. code-block:: bash

    $ php app/console gitonomy:config:show

    name             Gitonomy
    baseline         git repositories inside your infrastructure
    ssh_access       git@example.org
    repository_path  /path/to/repositories

If you want to get a specific value:

.. code-block:: bash

    $ php app/console gitonomy:config:show repository_path

    /path/to/repositories
