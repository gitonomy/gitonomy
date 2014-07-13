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
use Gitonomy\Bundle\CoreBundle\Entity\ProjectGitAccess;
use Gitonomy\Bundle\CoreBundle\Git\PushTarget;
use Gitonomy\Bundle\CoreBundle\Security\ProjectRole;

class GitAccessVoter implements VoterInterface
{
    protected $prefix;

    public function __construct($prefix = 'GIT_')
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

        $type = ProjectGitAccess::WRITE_PERMISSION;
        foreach ($attributes as $attribute) {
            switch ($attribute) {
                case 'GIT_DELETE';
                case 'GIT_FORCE':
                    $type = ProjectGitAccess::ADMIN_PERMISSION;
                    break 2;
            }
        }

        if (is_array($object) && isset($object[0]) && $object[0] instanceof Project) {
            $object = new PushTarget($object[0], $object[1]);
        }

        if (!$object instanceof PushTarget) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        foreach ($object->getProject()->getGitAccesses() as $access) {
            if ($access->isGranted($user, $object->getReference(), $type)) {
                return self::ACCESS_GRANTED;
            }
        }

        return self::ACCESS_DENIED;
    }
}
