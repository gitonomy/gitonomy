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

use Gitonomy\Component\EventDispatcher\AsyncEvent;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class AsyncEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSignature()
    {
        $event = new AsyncEvent('foo', new Event());
        $this->assertNotNull($event->getSignature(), "Signature is generated");

        $otherEvent = new AsyncEvent('foo', new Event());
        $this->assertNotNull($otherEvent->getSignature(), "Other signature is generated");

        $this->assertNotEquals($event->getSignature(), $otherEvent->getSignature(), "Signatures are different");

        $event = new AsyncEvent('foo', new Event(), 'bar');
        $this->assertEquals('bar', $event->getSignature(), "Signature is passed one");
    }
}
