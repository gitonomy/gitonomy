<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class Project extends Base\BaseProject
{
    const SLUG_PATTERN = '[a-z0-9-_]+';

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Returns the user role of a given user.
     *
     * @return Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject The user role on the project
     *
     * @throws InvalidArgumentException Throws an exception if no role was found for the given user on the project.
     */
    public function getUserRole(User $user)
    {
        foreach ($this->userRoles as $userRole) {
            if ($user->equals($userRole->getUser())) {
                return $userRole;
            }
        }

        throw new \InvalidArgumentException('No role for user on project');
    }
}
