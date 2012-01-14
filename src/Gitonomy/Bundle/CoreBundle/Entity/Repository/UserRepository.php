<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Gitonomy\Bundle\CoreBundle\Entity;

class UserRepository extends EntityRepository
{
    public function findOneByEmail($email)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->leftJoin('u.emails', 'e')
            ->where('e.email = :email')
            ->setParameters(array(
                'email' => $email
            ))
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
        ;
    }

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
