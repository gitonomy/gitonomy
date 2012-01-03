<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\FrontendBundle\Model\Security\ForgotPasswordRequest;

/**
 * Controller for security actions.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class SecurityController extends BaseController
{
    /**
     * Registration page.
     */
    public function registerAction()
    {
        if (!$this->container->getParameter('gitonomy_frontend.user.open_registration')) {
            throw $this->createNotFoundException('Public registration is disabled');
        }

        $user = new User();
        $user->setTimezone(date_default_timezone_get());
        $form = $this->createForm('user_registration', $user);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->encodePasswordAndSave($user);
                $this->get('session')->setFlash('success', 'Your account was created!');

                return $this->redirect($this->generateUrl('gitonomyfrontend_main_homepage'));
            } else {
                $this->get('session')->setFlash('warning', 'Roger, we have a problem with your form');
            }
        }

        return $this->render('GitonomyFrontendBundle:Security:register.html.twig', array(
            'form' => $form->createView()
        ));
    }

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
            $user = $handler->getUserIfForgotPasswordTokenValid($username, $forgotPasswordToken);
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundHttpException('This token is not valid', $e);
        }

        $form = $this->createForm('change_password', $user);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $user->removeForgotPasswordToken();
                $this->encodePasswordAndSave($user);
                $this->get('session')->setFlash('success', 'You password has changed!');

                return $this->redirect($this->generateUrl('gitonomyfrontend_main_homepage'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Security:changePassword.html.twig', array(
            'form'     => $form->createView(),
            'user'     => $user
        ));
    }

    /**
     * Encode the password of a user and save it.
     *
     * @param Gitonomy\Bundle\CoreBundle\Entity\User $user A user to register
     */
    protected function encodePasswordAndSave(User $user)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword($user->getPassword(), $user->regenerateSalt()));

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
    }
}
