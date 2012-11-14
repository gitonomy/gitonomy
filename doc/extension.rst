Extend Gitonomy
===============

Register your listener
::::::::::::::::::::::

Declare a service in container and tag it as a ``gitonomy.event_listener``:

.. code-block:: xml

    <service id="your_listener_id" class="...">
        <tag name="gitonomy.event_listener" event="gitonomy.project_push" method="onPush" />
    </service>


Events
::::::

gitonomy.project_create
-----------------------

**class**: Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent

gitonomy.project_push
---------------------

**class**: Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\PushReferenceEvent

gitonomy.project_delete
-----------------------

**class**: Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent
