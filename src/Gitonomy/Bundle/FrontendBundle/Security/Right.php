<?php

namespace Gitonomy\Bundle\FrontendBundle\Security;

use Gitonomy\Bundle\CoreBundle\Entity;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class Rights to define permissions for a logged user.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class Right
{
    protected $token;
    protected $em;

    public function __construct(SecurityContext $securityContext, EntityManager $entityManager)
    {
        $this->token = $securityContext->getToken();
        $this->em    = $entityManager;
    }

    public function isGranted($token, $permission, $project = null)
    {
        if (!$token->getUser() instanceof UserInterface) {
            throw new BadCredentialsException('Authentication required');
        }

        $user = $token->getUser();

        if (!$this->hasPermission($user, $permission)) {
            throw new HttpException(403, 'Unautorized');
        }

        return (null !== $this->findUserRole($user, $project, $permission));
    }

    public function isCurrentUserGranted($permission, $project = null)
    {
        return $this->isGranted($this->token, $permission, $project);
    }

    protected function hasPermission($user, $permissions)
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

    protected function findUserRole($user, $project, $permission)
    {
        return $this->em->getRepository('GitonomyCoreBundle:Permission')->findByPermission($user, $project, $permission);
    }
}