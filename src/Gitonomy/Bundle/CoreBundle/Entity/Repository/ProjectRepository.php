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

class ProjectRepository extends EntityRepository
{
    public function findAll()
    {
        return $this
            ->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByUser(Entity\User $user)
    {
        return $this
            ->createQueryBuilder('p')
            ->leftJoin('p.userRoles', 'ur')
            ->where('ur.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute()
        ;
    }

    public function findByUsers($users)
    {
        $qb = $this->createQueryBuilder('p');

        foreach ($users as $i => $user) {
            $alias     = 'ur_'.$i;
            $parameter = 'user_'.$i;
            $qb
                ->leftJoin('p.userRoles', $alias)
                ->andWhere($alias.'.user = :'.$parameter)
                ->setParameter($parameter, $user)
            ;
        }

        return $qb->getQuery()->execute();
    }
}
