<?php

namespace Gitonomy\Bundle\FrontendBundle\Security;

use Doctrine\ORM\EntityManager;

use Gitonomy\Bundle\CoreBundle\Entity;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\Project;

/**
 * Class Rights to define permissions for a logged user.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class Right
{
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em    = $entityManager;
    }

    public function isGrantedForProject(User $user, Project $project, $permission)
    {
        return $this->em
            ->getRepository('GitonomyCoreBundle:Permission')
            ->hasProjectPermission($user, $project, $permission)
        ;
    }

    public function isGranted(User $user, $permission)
    {
        return $this->hasPermission($user, $permission);
    }

    protected function hasPermission(User $user, $permissions)
    {
        if (!is_array($permissions)) {
            return in_array($permissions, $user->getRoles());
        } else {
            foreach ($permissions as $permission) {
                if (in_array($permission, $user->getRoles())) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function getPermissions($user)
    {
        return $user->getRoles();
    }
}
