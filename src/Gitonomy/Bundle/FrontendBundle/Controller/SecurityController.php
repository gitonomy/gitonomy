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

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\FrontendBundle\Model\Security\ForgotPasswordRequest;

/**
 * Controller for security actions.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class SecurityController extends BaseController
{
    public function forgotPasswordAction()
    {
        $forgotPasswordRequest = new ForgotPasswordRequest();
        $form = $this->createForm('forgot_password_request', $forgotPasswordRequest);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->get('gitonomy_frontend.security.forgot_password_handler')->processRequest($forgotPasswordRequest);
                $this->get('session')->setFlash('success', 'We send a mail with instructions. Read it, and click, now!');

                return $this->redirect($this->generateUrl('gitonomyfrontend_security_forgotPassword'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Security:forgotPassword.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function changePasswordAction($username, $forgotPasswordToken)
    {
        $handler = $this->get('gitonomy_frontend.security.forgot_password_handler');
        try {
            $user = $this->getDoctrine()->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
            $user = $handler->validate($user, $forgotPasswordToken);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('This token is not valid', $e);
        }

        $form = $this->createForm('change_password', $user);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $handler->removeForgotPasswordToken($user);

                $this->get('session')->setFlash('success', 'You password has changed!');

                return $this->redirect($this->generateUrl('gitonomyfrontend_main_homepage'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Security:changePassword.html.twig', array(
            'form'  => $form->createView(),
            'user'  => $user,
            'token' => $forgotPasswordToken
        ));
    }
}
