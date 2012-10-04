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

namespace Gitonomy\Component\EventDispatcher\Tests;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\Container;

use Gitonomy\Component\EventDispatcher\EventDispatcher;
use Gitonomy\Component\EventDispatcher\AsyncEvent;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testStorage()
    {
        $storage = $this->getMock('Gitonomy\Component\EventDispatcher\EventStorageInterface');

        $storage
            ->expects($this->once())
            ->method('store')
        ;

        $container = new Container();
        $dispatcher = new EventDispatcher($container);
        $dispatcher->setStorage($storage);

        $test = $this;
        $dispatcher->addListener('foo', function () {
            $test->fail();
        });

        $dispatcher->dispatchAsync('foo', new Event());
    }

    public function testRunAsyncSuccess()
    {
        $storage = $this->getMock('Gitonomy\Component\EventDispatcher\EventStorageInterface');
        $asyncEvent = new AsyncEvent('foo', new Event(), 'f');

        $storage
            ->expects($this->once())
            ->method('getNextToProcess')
            ->will($this->returnValue($asyncEvent))
        ;

        $storage
            ->expects($this->once())
            ->method('acknowledge')
            ->with($this->equalTo($asyncEvent), $this->equalTo(true))
        ;

        $dispatcher = new EventDispatcher(new Container());
        $dispatcher->setStorage($storage);

        $ran = false;
        $dispatcher->addListener('foo', function () use (&$ran) {
            $ran = true;
        });

        $result = false;
        $dispatcher->runAsync();

        $this->assertTrue($ran, 'Listener was successfully called');
    }

    public function testRunAsyncError()
    {
        $storage = $this->getMock('Gitonomy\Component\EventDispatcher\EventStorageInterface');
        $asyncEvent = new AsyncEvent('foo', new Event(), 'f');

        $storage
            ->expects($this->once())
            ->method('getNextToProcess')
            ->will($this->returnValue($asyncEvent))
        ;

        $storage
            ->expects($this->once())
            ->method('acknowledge')
            ->with($this->equalTo($asyncEvent), $this->equalTo(false))
        ;

        $dispatcher = new EventDispatcher(new Container());
        $dispatcher->setStorage($storage);

        $dispatcher->addListener('foo', function () use (&$ran) {
            throw new \Exception('fail');
        });

        $result = false;

        try {
            $dispatcher->runAsync();
            $this->fail("runAsync should throw listener exception");
        } catch (\Exception $e) {
        }
    }
}
