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

namespace Gitonomy\Bundle\CoreBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Security\ProjectRole;

class ProjectRoleVoter implements VoterInterface
{
    protected $prefix;

    public function __construct($prefix = 'PROJECT_')
    {
        $this->prefix = $prefix;
    }

    public function supportsAttribute($attribute)
    {
        return $this->prefix === substr($attribute, 0, strlen($this->prefix));
    }

    public function supportsClass($class)
    {
        return true;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return self::ACCESS_DENIED;
        }

        if (!$object instanceof Project) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $roles = array();
        foreach ($user->getRoles() as $role) {
            if ($role->getRole() === 'ROLE_ADMIN') {
                return self::ACCESS_GRANTED;
            }
            if ($role instanceof ProjectRole && $role->isProject($object)) {
                $roles[] = $role->getProjectRole();
            }
        }
        $attributes = array_diff($attributes, $roles);

        return count($attributes) ? self::ACCESS_DENIED : self::ACCESS_GRANTED;
    }
}
