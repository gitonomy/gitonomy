Install Gitonomy TwigBundle in a Symfony2 application
=====================================================

To start using this bundle, first, add the bundle to your ``composer.json``:

.. code-block:: json

    {
        "require": {
            "gitonomy/twig-bundle": "dev-master"
        }
    }

Then, add a line to your *AppKernel*:

.. code-block:: php

    class AppKernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Gitonomy\Bundle\TwigBundle()
            );
        }
    }

In your *config.yml* file, you need to enable the twig extension:

.. code-block:: yaml

    gitonomy_twig:
        twig_extension:
            enabled: true

Configure routing
-----------------

For links, this extension will rely on a "default" routing implementation,
expecting some given routes with given arguments to be defined.

If you want to parameter those routes, you can configure it:

.. code-block:: yaml

    # Gitonomy Twig
    gitonomy_twig:
        twig_extension:
            # ...
            routes_names:
                commit:               project_commit
                branch:               project_history
                tag:                  project_history
                tree:                 project_tree
            routes_args:
                commit_repository:    slug
                commit_hash:          hash
                branch_repository:    slug
                branch_name:          branch
                tag_repository:       slug
                tag_name:             branch
                tree_repository:      slug
                tree_revision:        revision
                tree_path:            path

Inject git assets
-----------------

To make blocks look awesome, we used CSS and Javascript to make your experience better.
Since integration of assets really depend of how your application manage them,
this bundle won't try to load them automatically.

If you are using Assetic:

.. code-block:: html+jinja

    {% stylesheets
        "bundles/gitonomytwig/css/diff.css"
        "bundles/gitonomytwig/css/log.css"
    %}
        <link rel="stylesheet" href="{{ asset(asset_url) }}" />
    {% endstylesheets %}

    {% javascripts
        "bundles/gitonomytwig/js/log.js"
    %}
        <script type="text/javascript" src="{{ asset(asset_url) }}"></script>
    {% endjavascripts %}

If not, you just need to make your application load those stylesheets:

* @GitonomyTwigBundle/Resources/public/css/diff.css
* @GitonomyTwigBundle/Resources/public/css/log.css

And those javascripts:

* @GitonomyTwigBundle/Resources/public/css/log.js
