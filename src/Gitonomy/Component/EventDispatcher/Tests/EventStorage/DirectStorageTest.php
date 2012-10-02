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

namespace Gitonomy\Component\EventDispatcher\Tests\EventStorage;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

use Gitonomy\Component\EventDispatcher\AsyncEvent;
use Gitonomy\Component\EventDispatcher\EventStorage\DirectStorage;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class DirectStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testStore()
    {
        $dispatcher = new EventDispatcher();
        $storage    = new DirectStorage($dispatcher);

        $called = false;
        $dispatcher->addListener('foo', function () use (&$called) {
            $called = true;
        });

        $storage->store(new AsyncEvent('foo', new Event()));

        $this->assertTrue($called, "Listener was called on store");
        $this->assertNull($storage->getNextToProcess(), "Nothing to process in storage");
    }

    public function testGetNextToProcess()
    {
        $dispatcher = new EventDispatcher();
        $storage    = new DirectStorage($dispatcher);

        $this->assertNull($storage->getNextToProcess(), "Nothing to process in storage");
    }
}
