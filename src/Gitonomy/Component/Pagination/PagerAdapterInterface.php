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

interface PagerAdapterInterface
{
    /**
     * Fetch a subset of data.
     *
     * @param int $offset Starting offset (0 indexed)
     * @param int $limit  Limit the number of results
     */
    public function get($offset, $limit);

    /**
     * Count number of elements in data.
     */
    public function count();
}
