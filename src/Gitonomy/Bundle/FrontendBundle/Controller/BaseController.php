<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\Project;

/**
 * Base class for every controller of the frontend.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class BaseController extends Controller
{
    /**
     * Checks the current security context grants a given role.
     *
     * @param string $permission The permission to check (can be an array, meaning OR)
     * @param string $message An error message (internal, will not be displayed)
     */
    protected function assertPermission($permission, $message = 'Access Denied')
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->get('gitonomy_frontend.security.right')->isGranted($user, $permission)) {
            throw new AccessDeniedException($message);
        }
    }

    /**
     * Checks the current security context grants a given role to the user for a project.
     *
     * @param Gitonomy\Bundle\CoreBundle\Entity\Project $project The project to check
     * @param mixed $permission A permission name (or an array, meaning OR)
     * @param string $message An error message (internal, will not be displayed)
     */
    protected function assertProjectPermission(Project $project, $permission, $message = 'Access Denied')
    {
        $user = $this->getUser();

        if (!$user instanceof User || !$this->get('gitonomy_frontend.security.right')->isGrantedForProject($user, $project, $permission)) {
            throw new AccessDeniedException($message);
        }
    }
}
