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

namespace Gitonomy\Component\Pagination\Tests;

use Gitonomy\Component\Pagination\Pager;
use Gitonomy\Component\Pagination\Adapter\ArrayAdapter;

class PagerTest extends \PHPUnit_Framework_TestCase
{
    private function createPager($value)
    {
        if (is_array($value)) {
            $array = $value;
        } else {
            $array = (array) new \SplFixedArray($value);
        }

        return new Pager(new ArrayAdapter($array));
    }

    public function testEmpty()
    {
        $pager = $this->createPager(0);

        $this->assertEquals(0, $pager->count(),        "Empty pager returns 0 elements");
        $this->assertEquals(0, $pager->getPageCount(), "Empty pager returns 0 pages");
        $this->assertEquals(0, $pager->getOffset(),    "Offset is 0 by default");
        $this->assertEquals(1, $pager->getPage(),      "Default to first page");
    }

    public function testSimple()
    {
        $pager = $this->createPager(array('a', 'b', 'c', 'd'));

        $pager->setPerPage(2);
        $pager->setPage(1);
        $this->assertEquals(array('a', 'b'), (array) $pager->getIterator());
        $pager->setPage(2);
        $this->assertEquals(array('c', 'd'), (array) $pager->getIterator());

        $pager->setPerPage(1);
        $pager->setPage(1);
        $this->assertEquals(array('a'), (array) $pager->getIterator());
        $pager->setPage(2);
        $this->assertEquals(array('b'), (array) $pager->getIterator());
        $pager->setPage(4);
        $this->assertEquals(array('d'), (array) $pager->getIterator());
    }

    public function testPerPage()
    {
        $pager = $this->createPager(54);
    }

    public function provideRoundingPagination()
    {
        return array(
            array(0,  0),
            array(1,  1),
            array(29, 3),
            array(30, 3),
            array(31, 4)
        );
    }

    /**
     * @dataProvider provideRoundingPagination
     */
    public function testRoundingPagination($count, $pageCount)
    {
        $pager = $this->createPager($count);

        $this->assertEquals($count,     $pager->count(),        "Global counting");
        $this->assertEquals($pageCount, $pager->getPageCount(), "Count of pages");
    }
}
