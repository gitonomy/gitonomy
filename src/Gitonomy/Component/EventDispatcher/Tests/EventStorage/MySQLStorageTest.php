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

use Symfony\Component\EventDispatcher\Event;

use Gitonomy\Component\EventDispatcher\EventStorage\MySQLStorage;
use Gitonomy\Component\EventDispatcher\AsyncEvent;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class MySQLStorageTest extends \PHPUnit_Framework_TestCase
{
    protected $mock;

    public function setUp()
    {
        $this->mock = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->serializer = $this->getMock('Symfony\Component\Serializer\SerializerInterface');
    }

    public function testCreateTable()
    {
        $this->mock
            ->expects($this->once())
            ->method('executeQuery')
            ->with($this->matchesRegularExpression('/^CREATE TABLE/'));
        ;

        $storage = new MySQLStorage($this->serializer, $this->mock);
    }

    public function testCreateTableDisabled()
    {
        $this->mock
            ->expects($this->never())
            ->method('executeQuery')
        ;

        $storage = new MySQLStorage($this->serializer, $this->mock, false);
    }

    public function testStore_InsertRow()
    {
        $this->mock
            ->expects($this->once())
            ->method('executeQuery')
            ->with($this->matchesRegularExpression('/^INSERT INTO/'));
        ;

        $storage = new MySQLStorage($this->serializer, $this->mock, false);
        $storage->store(new AsyncEvent('foo', new Event()));
    }

    public function testGetNextToProcess_SelectRowWithNoResult()
    {
        $rs = $this->getMock('PDOStatement');
        $rs->expects($this->once())->method('fetch')->will($this->returnValue(false));

        $this->mock
            ->expects($this->once())
            ->method('executeQuery')
            ->will($this->returnValue($rs));
        ;

        $storage = new MySQLStorage($this->serializer, $this->mock, false);
        $this->assertNull($storage->getNextToProcess(), "Storage returns null when no result in statement");
    }

    public function testGetNextToProcess_SelectRowWithResult()
    {
        $event = new Event();
        $rs = $this->getMock('PDOStatement');
        $rs->expects($this->once())->method('fetch')->will($this->returnValue(array(
            'eventName'       => 'foo',
            'eventType'       => 'foo',
            'eventSerialized' => '',
            'signature'       => 'bar'
        )));

        $this->mock->expects($this->at(0))
            ->method('executeQuery')
            ->with($this->matchesRegularExpression('/^SELECT /'))
            ->will($this->returnValue($rs))
        ;

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->will($this->returnValue($event))
        ;

        $storage = new MySQLStorage($this->serializer, $this->mock, false);
        $asyncEvent = $storage->getNextToProcess();

        $this->assertTrue($asyncEvent instanceof AsyncEvent, "Async event returned correctly");
        $this->assertEquals("foo", $asyncEvent->getEventName(), "Event name is correct");
        $this->assertEquals("bar", $asyncEvent->getSignature(), "Signature is correct");
        $this->assertTrue($asyncEvent->getEvent() instanceof Event, "Event is correct type");
    }

    public function testAcknowledgeError_WithRequeue_UpdateRow()
    {
        $this->mock
            ->expects($this->once())
            ->method('executeQuery')
            ->with($this->matchesRegularExpression('/^UPDATE/'));
        ;

        $storage = new MySQLStorage($this->serializer, $this->mock, false);
        $storage->acknowledge(new AsyncEvent('foo', new Event()), false);
    }

    public function testAcknowledge_ErrorWithoutRequeue_DeleteRow()
    {
        $this->mock
            ->expects($this->once())
            ->method('executeQuery')
            ->with($this->matchesRegularExpression('/^DELETE/'));
        ;

        $storage = new MySQLStorage($this->serializer, $this->mock, false, false);
        $storage->acknowledge(new AsyncEvent('foo', new Event()), false);
    }

    public function testAcknowledgeSuccess_DeleteRow()
    {
        $this->mock
            ->expects($this->once())
            ->method('executeQuery')
            ->with($this->matchesRegularExpression('/^DELETE/'));
        ;

        $storage = new MySQLStorage($this->serializer, $this->mock, false);
        $storage->acknowledge(new AsyncEvent('foo', new Event()), true);
    }
}
