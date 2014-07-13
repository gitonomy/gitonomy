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

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Gitonomy\Component\Pagination\PagerAdapterInterface;

class DoctrineOrmQueryAdapter implements PagerAdapterInterface
{
    private $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function get($offset, $limit)
    {
        $q = clone $this->query;
        $q->setParameters(clone $this->query->getParameters());
        $q->setFirstResult($offset)->setMaxResults($limit);

        return $q->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $paginator = new Paginator($this->query);

        return $paginator->count();
    }
}
