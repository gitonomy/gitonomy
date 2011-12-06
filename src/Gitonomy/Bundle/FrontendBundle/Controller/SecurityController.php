<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

/**
 * Controller for security actions.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class SecurityController extends BaseController
{
    /**
     * Login action.
     */
    public function loginAction()
    {
        $request = $this->getRequest();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('GitonomyFrontendBundle:Security:login.html.twig', array(
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    /**
     * Check the login credentials.
     */
    public function loginCheckAction()
    {
        throw new \LogicException("You should not view this page !");
    }
}
