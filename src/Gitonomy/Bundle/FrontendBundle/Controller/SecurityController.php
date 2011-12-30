<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

use Gitonomy\Bundle\FrontendBundle\Model\Security\ForgotPasswordRequest;

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
            $request->getSession()->remove(SecurityContext::AUTHENTICATION_ERROR);
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

    public function forgotPasswordAction()
    {
        $forgotPasswordRequest = new ForgotPasswordRequest();
        $form = $this->createForm('forgot_password_request', $forgotPasswordRequest);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->get('gitonomy_frontend.security.forgot_password_handler')->processRequest($forgotPasswordRequest);
            }
        }


        return $this->render('GitonomyFrontendBundle:Security:forgotPassword.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
