<?php

namespace Gitonomy\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gitonomy\Bundle\CoreBundle\Entity;

class ProjectRepository extends EntityRepository
{
    public function findByUser(Entity\User $user)
    {
        return $this
            ->createQueryBuilder('p')
            ->leftJoin('p.userRoles', 'ur')
            ->where('ur.user = :user')
            ->setParameters(array(
                'user' => $user
            ))
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
