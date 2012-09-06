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
