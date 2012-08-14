<?php

namespace Gitonomy\Component\Pagination\Tests\Adapter;

use Gitonomy\Component\Pagination\Adapter\ArrayAdapter;

class ArrayAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testAll()
    {
        $adapter = new ArrayAdapter(array(1, 2, 3, 4, 5, 6, 7, 8));
        $this->assertEquals(8, $adapter->count(), 'Count works correctly');
        $this->assertEquals(array(2, 3, 4), $adapter->get(1, 3), "Test a slice");
    }
}
