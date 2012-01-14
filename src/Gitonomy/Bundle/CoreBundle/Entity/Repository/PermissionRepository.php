<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Gitonomy\Bundle\CoreBundle\Entity;

class PermissionRepository extends EntityRepository
{
    public function hasProjectPermission(Entity\User $user, Entity\Project $project, $permission)
    {
        $count = $this
            ->createQueryBuilder('permission')
            ->select('COUNT(permission.id)')
            ->leftJoin('permission.parent', 'permission_parent')
            ->leftJoin('permission.roles', 'role')
            ->leftJoin('role.userRolesProject', 'user_role_project')
            ->leftJoin('user_role_project.user', 'user')
            ->leftJoin('user_role_project.project', 'project')
            ->where('user.id = :user_id AND project.id = :project_id AND ( permission.name = :permission OR permission_parent.name = :permission )')
            ->setParameters(array(
                'user_id'    => $user->getId(),
                'project_id' => $project->getId(),
                'permission' => $permission,
            ))
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count >= 1;
    }
}