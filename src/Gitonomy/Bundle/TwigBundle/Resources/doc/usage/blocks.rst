Rendering git blocks
====================

Graph log
---------

Now, you should be able to use Twig functions to render git blocks::

The PHP part:

.. code-block:: php

    $repository = new Gitonomy\Git\Repository('/path/to/repository');

    echo $twig->render('template.html.twig', array(
        'log' => $repository->getLog() # returns a log of all references
    ));

The Twig part:

.. code-block:: html+jinja

    {# Render a log graph #}
    {{ git_log(log) }}

By default, this will display all rows of the log. If you have thousands of lines,
it will display thousands of lines...

The best way to avoid this is to set a limit to the log (and/or an offset):

.. code-block:: php

    $log = $repository->getLog();

    $log->setLimit(20);
    $log->setOffset(0);

You can paginate log on your own. If you want some Ajax love, use the option
``query_url`` when rendering the git block:

.. code-block:: html+jinja

    {{ git_log(log, {query_url: '/log-ajax'}) }}

This parameter indicates to the twig bundle to call this URL to get
fragment of lines to append. This URL will be called with "offset"
and "limit" in query string:

.. code-block:: php

    $repository = new Repository('/path/to/repository');
    $repository
        ->setOffset($_GET['offset'] ?: 0)
        ->setLimit($_GET['limit'] ?: 20)
    ;

    return $twig->render('template.html.twig', array(
        'log' => $log,
        'ajax' => true
    ));

The template:

.. code-block:: html+jinja

    {% if ajax %}
        {{ git_log_rows(log, {query_url: '/log-ajax'}) }}
    {% else %}
        {{ git_log(log, {query_url: '/log-ajax'}) }}
    {% endif %}
