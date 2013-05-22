GitonomyTwigBundle
==================

This bundle provides git features for your twig-based application.

Twig extension for git
----------------------

This extension provides nice blocks for your git-based application:

* Author blocks
* git logs
* diff
* commit view

To enable it, edit your ``config.yml`` file to add:

    gitonomy_twig:
        extension: true

You will need to include Javascript and CSS located in bundle if you want
to enjoy some CSS:

* @GitonomyTwigBundle/Resources/css/*.css
* @GitonomyTwigBundle/Resources/js/*.js
