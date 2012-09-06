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
use Doctrine\ORM\Query;

class UserSshKeyRepository extends EntityRepository
{
    public function getKeyList()
    {
        return $this->createQueryBuilder('k')
            ->leftJoin('k.user', 'u')
            ->select('u.username, k.content')
            ->getQuery()
            ->setHydrationMode(Query::HYDRATE_SCALAR)
            ->execute()
        ;
    }

    public function markAllAsInstalled()
    {
        $this->createQueryBuilder('k')
            ->update()
            ->set('k.isInstalled', true)
            ->getQuery()
            ->execute()
        ;
    }
}
