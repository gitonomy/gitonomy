<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Component\EventDispatcher\EventStorage;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Gitonomy\Component\EventDispatcher\EventStorageInterface;
use Gitonomy\Component\EventDispatcher\AsyncEvent;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class DirectStorage implements EventStorageInterface
{
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function store(AsyncEvent $event)
    {
        $this->dispatcher->dispatch($event->getEventName(), $event->getEvent());
    }

    public function getNextToProcess()
    {
        return null;
    }

    public function acknowledge(AsyncEvent $event, $isSuccess)
    {
    }
}
