Generating links
================

Once you have installed the extension, you can also generate links to various
elements: commit, reference, branch, tag, tree.

Link to a commit
----------------

.. code-block:: html+jinja

    {{ git_url(commit) }}


Link to a branch
----------------

.. code-block:: html+jinja

    {{ git_url(branch) }}

Link to a tag
----------------

.. code-block:: html+jinja

    {{ git_url(tag) }}

Link to a tree
--------------

.. code-block:: html+jinja

    {{ git_url(branch, {path: '/src'}) }}
    {# or #}
    {{ git_url(branch, {path: ''}) }}
