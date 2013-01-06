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

namespace Gitonomy\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Gitonomy\Bundle\CoreBundle\Entity;

class RoleRepository extends EntityRepository
{
    public function getIndexedByName($names)
    {
        $qb = $this->createQueryBuilder('r');
        $res = $qb->where($qb->expr()->in('r.name', $names))->getQuery()->execute();

        $return = array();
        foreach ($res as $role) {
            $return[$role->getName()] = $role;
        }

        return $return;
    }
}
