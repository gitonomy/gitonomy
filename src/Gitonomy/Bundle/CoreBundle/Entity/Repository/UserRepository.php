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
        $em    = $this->getManager();
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
