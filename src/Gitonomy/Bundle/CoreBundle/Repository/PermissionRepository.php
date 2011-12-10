<?php

namespace Gitonomy\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gitonomy\Bundle\CoreBundle\Entity;

class PermissionRepository extends EntityRepository
{
    public function findByPermission(Entity\User $user, Entity\Project $project, $permission)
    {
        $query = $this
            ->getEntityManager()
            ->createQuery(<<<QUERY
     SELECT UR
       FROM GitonomyCoreBundle:UserRole UR
 INNER JOIN UR.role                     R
 INNER JOIN R.rolePermissions           RP
 INNER JOIN RP.permission               RP
      WHERE UR.user           = :user_id
        AND UR.project        = :project_id
        AND P.permission      = :permission
QUERY
            )->setParameters(array(
                'user_id'    => $user->getId(),
                'project_id' => (null !== $project) ? $project->getId() : null,
                'permission' => $permission,
            )
        );

        return $query->getSingleResult();
    }
}