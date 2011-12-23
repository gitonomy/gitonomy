<?php

namespace Gitonomy\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gitonomy\Bundle\CoreBundle\Entity;

class UserRepository extends EntityRepository
{
    public function findByProject(Entity\Project $project)
    {
        $em    = $this->getEntityManager();
        $query = $em
            ->createQuery(<<<SQL
SELECT U
  FROM GitonomyCoreBundle:User U
INNER JOIN U.projectRoles UR
 WHERE UR.project = :projectId
SQL
            )
            ->setParameter('projectId', $project->getId())
        ;

        return $query->getResult();
    }
}
