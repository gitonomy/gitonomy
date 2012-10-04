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

namespace Gitonomy\Component\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class EventDispatcher extends ContainerAwareEventDispatcher
{
    protected $storage;

    public function setStorage(EventStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function dispatchAsync($eventName, Event $event)
    {
        $this->requireStorage()->store(new AsyncEvent($eventName, $event));
    }

    public function runAsync()
    {
        $storage    = $this->requireStorage();
        $asyncEvent = $storage->getNextToProcess();

        if (!$asyncEvent) {
            return false;
        }

        try {
            $this->dispatch($asyncEvent->getEventName(), $asyncEvent->getEvent());
            $storage->acknowledge($asyncEvent, true);
        } catch (\Exception $e) {
            $storage->acknowledge($asyncEvent, false);

            throw $e;
        }

        return true;
    }

    protected function requireStorage()
    {
        if (null === $this->storage) {
            throw new \LogicException(sprintf('no asynchronous storage found in event dispatcher.', $eventName));
        }

        return $this->storage;
    }
}
