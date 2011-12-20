<?php

namespace Gitonomy\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gitonomy\Bundle\CoreBundle\Entity;

class ProjectRepository extends EntityRepository
{
    public function findUsedProjectsForUser(Entity\User $user)
    {
        $em    = $this->getEntityManager();
        $query = $em
            ->createQuery(<<<SQL
SELECT P
  FROM GitonomyCoreBundle:Project P
INNER JOIN P.userRoles UR
 WHERE UR.user = :userId
SQL
            )
            ->setParameter('userId', $user->getId())
        ;

        return $query->getResult();
    }
}
