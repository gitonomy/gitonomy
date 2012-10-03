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

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
interface EventStorageInterface
{
    public function store(AsyncEvent $event);
    /**
     * @return AsyncEvent
     */
    public function getNextToProcess();
    public function acknowledge(AsyncEvent $event, $isSuccess);
}
