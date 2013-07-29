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

namespace Gitonomy\Component\Config\Tests;

use Gitonomy\Component\Config\ChainConfig;
use Gitonomy\Component\Config\ArrayConfig;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ChainConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testElementCount()
    {
        $mock = $this->createConfig();

        // 0 configs
        try {
            $config = new ChainConfig(array());
            $this->fail("Constructed ChainConfig with no arguments");
        } catch (\Exception $e) {}

        // 1 config
        try {
            $config = new ChainConfig(array($mock));
            $this->fail("Constructed ChainConfig with one argument");
        } catch (\Exception $e) {}

        // 2 config = ok
        $config = new ChainConfig(array($mock, $mock));
    }

    public function testGetWithExistingValue()
    {
        $first  = $this->createConfigMock();
        $second = $this->createConfigMock();

        $first->expects($this->once())->method('all')->will($this->returnValue(array('foo' => 'bar')));
        $second->expects($this->never())->method('all');

        $chain = new ChainConfig(array($first, $second));

        $this->assertEquals('bar', $chain->get('foo'));
    }

    public function testGetWithNotExistingValue()
    {
        $first  = $this->createConfigMock();
        $second = $this->createConfigMock();

        $first->expects($this->once())->method('all')->will($this->returnValue(array()));
        $first->expects($this->once())->method('setAll')->with(array('foo' => 'bar'));
        $second->expects($this->once())->method('all')->will($this->returnValue(array('foo' => 'bar')));

        $chain = new ChainConfig(array($first, $second));

        $this->assertEquals('bar', $chain->get('foo'));
    }

    public function testSet()
    {
        $first  = $this->createConfigMock();
        $second = $this->createConfigMock();

        $first->expects($this->once())->method('all')->will($this->returnValue(array()));
        $second->expects($this->once())->method('all')->will($this->returnValue(array()));
        $first->expects($this->once())->method('setAll')->with(array('foo' => 'bar'));
        $second->expects($this->once())->method('setAll')->with(array('foo' => 'bar'));

        $chain = new ChainConfig(array($first, $second));

        $chain->set('foo', 'bar');
    }

    public function testGetWithFailing()
    {
        $first  = $this->createConfigMock();
        $second = $this->createConfigMock();

        $first->expects($this->once())->method('all')->will($this->throwException(new \Exception('bar')));
        $second->expects($this->once())->method('all')->will($this->returnValue(array('foo' => 'baz')));

        $chain = new ChainConfig(array($first, $second));

        $this->assertEquals('baz', $chain->get('foo'));
    }

    public function testAllWithFailing()
    {
        $first  = $this->createConfigMock();
        $second = $this->createConfigMock();

        $first->expects($this->once())->method('all')->will($this->throwException(new \Exception('bar')));
        $second->expects($this->once())->method('all')->will($this->returnValue(array('foo' => 'baz')));

        $chain = new ChainConfig(array($first, $second));

        $this->assertEquals(array('foo' => 'baz'), $chain->all());
    }

    protected function createConfig($values = array())
    {
        return new ArrayConfig($values);
    }

    protected function createConfigMock()
    {
        return $this->getMock('Gitonomy\Component\Config\ConfigInterface');
    }
}
