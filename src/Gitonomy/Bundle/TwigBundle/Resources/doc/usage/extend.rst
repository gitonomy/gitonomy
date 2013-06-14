Extending Gitonomy TwigBundle
=============================

OK, now you're using this bundle and are happy with it. Great.
But how to override things to get more specific views?

Well good news, everything is meant to be overriden.

Override templating
-------------------

If you want to change the way blocks are rendered, create a new template file
and start writing blocks in it, like in *Resources/views/default_theme.html.twig*.

Here is an example:

.. code-block:: html-jinja

    {% block author %}
    {% spaceless %}
        <span class="git-author">
            {{- name -}}
        </span>
    {% endspaceless %}
    {% endblock %}

Symfony2
::::::::

When you created this file, you need to inject it in *GitExtension*.
If you are using the bundle, you can add it to configuration:

.. code-block:: yaml

    gitonomy_twig:
        twig_extension:
            enabled: true
            themes:
                - AcmeDemoBundle::my_theme.html.twig
                - GitonomyTwigBundle::default_theme.html.twig

Raw integration
:::::::::::::::

If you are using the Twig extension as standalone, you need to change the
second parameter of the constructor:

.. code-block:: php

    $extension = new GitExtension($urlGenerator, array(
        'my_theme.html.twig',
        '@GitonomyTwigBundle/default_theme.html.twig',
    ));

See `documentation on raw integration <./../install/raw.rst>`_ for more informations.
