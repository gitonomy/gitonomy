<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @param string $role The role to check
     * @param string $message An error message (internal, will not be displayed
     */
    protected function assertPermission($role, $message = 'Access Denied')
    {
        if (!$this->get('security.context')->isGranted($role)) {
            throw new AccessDeniedException($message);
        }
    }
}