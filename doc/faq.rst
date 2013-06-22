Frequently Asked Questions
==========================

Is Gitonomy good enough for me?
-------------------------------

OK, let's be fair: Gitonomy is still in early development.

We would love to conquer the world, make everybody use Gitonomy, paint the
world orange and start writing songs about it, unfortunately, it's an
open-source project.

We still lack some key features, and I have to tell you that some other
projects are more advanced than Gitonomy:

* `Github <https://github.com/>`_
* `Gitlab <http://gitlab.org/>`_
* `Gitorious <http://gitorious.org/>`_
* `Gitblit <http://gitblit.com/>`_

Please consider them as you consider Gitonomy. Gitonomy is good for you if you're
a Symfony2 developer and will probably want some day to hack in it.

Can I push using username/password?
-----------------------------------

By now, Gitonomy only works with SSH server using SSH keys. It's not possible to
authenticate with username/password credentials.

If you have any thoughts on this topic and sufficient skills for it, let's start
a discussion!

Can I push to repositories over HTTP?
-------------------------------------

Yay... not yet! Some experiences were made over HTTP, unfortunately, we are
still buffering all git packs inside the PHP process, which is a bad thing.

Same for here, if you have sufficient skills on this topic, I'd be happy to POC
with you.

Why are repositories located in cache folder?
---------------------------------------------

In version 0.3 and before, default location for repositories was
``app/cache/repositories``. Event worse, it was difficult to make
it work properly out of this folder.

Starting from version 0.4, default location is ``app/repositories``. Also, from
this version, it's possible to use shell scripts with a customly configured
repository location.

I modified ``parameters.yml`` but I'm still getting errors from Gitonomy. How to fix them?
------------------------------------------------------------------------------------------

When you modify your ``parameters.yml`` file, the cache of the application is
not automatically updated. You need to clear it manually:

.. code-block:: php

    php app/console cache:clear --env=prod

If you want to be sure it's properly cleaned, delete your ``app/cache/prod`` folder.
