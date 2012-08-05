<?php

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

        if (!$object instanceof Project) {
            return VoterInterface::ACCESS_DENIED;
        }

        $roles = array();
        foreach ($token->getRoles() as $role) {
            if ($role instanceof ProjectRole && $role->isProject($object)) {
                $roles[] = $role->getProjectRole();
            }
        }
        $attributes = array_diff($attributes, $roles);

        return count($attributes) ? self::ACCESS_DENIED : self::ACCESS_GRANTED;
    }
}
