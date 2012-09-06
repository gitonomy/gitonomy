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
use Gitonomy\Git\Log;

class GitLogAdapter implements PagerAdapterInterface
{
    private $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function get($offset, $limit)
    {
        $this->log->setOffset($offset);
        $this->log->setLimit($limit);

        return $this->log->getCommits();
    }

    public function count()
    {
        return $this->log->countCommits();
    }
}
