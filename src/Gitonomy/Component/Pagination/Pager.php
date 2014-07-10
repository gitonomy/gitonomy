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

namespace Gitonomy\Component\Pagination;

class Pager implements \IteratorAggregate, \Countable
{
    /**
     * @var PagerAdapterInteface
     */
    private $adapter;

    private $offset = 0;
    private $perPage;
    private $total;

    public function __construct(PagerAdapterInterface $adapter, $perPage = 10)
    {
        $this->adapter = $adapter;
        $this->perPage = $perPage;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function setPage($page)
    {
        $this->offset = (max(1, (int) $page) - 1) * $this->perPage;

        return $this;
    }

    public function isFirstPage()
    {
        return $this->getPage() == 1;
    }

    public function isLastPage()
    {
        return $this->getPage() == $this->getPageCount();
    }

    public function getPage()
    {
        return floor($this->offset/$this->perPage) + 1;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function setPerPage($perPage)
    {
        $this->perPage = (int) $perPage;
    }

    public function count()
    {
        if (null === $this->total) {
            $this->total = $this->adapter->count();
        }

        return $this->total;
    }

    /**
     * Can be zero.
     */
    public function getPageCount()
    {
        return ceil($this->count() / $this->perPage);
    }

    public function getResults()
    {
        return $this->getIterator();
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->adapter->get($this->offset, $this->perPage));
    }
}
