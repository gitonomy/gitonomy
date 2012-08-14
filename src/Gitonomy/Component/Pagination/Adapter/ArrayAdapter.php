<?php

namespace Gitonomy\Component\Pagination\Adapter;

use Gitonomy\Component\Pagination\PagerAdapterInterface;

class ArrayAdapter implements PagerAdapterInterface
{
    private $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function count()
    {
        return count($this->array);
    }

    public function get($offset, $limit)
    {
        return array_slice($this->array, $offset, $limit);
    }
}
