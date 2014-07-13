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

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

use Doctrine\ORM\NoResultException;

use Gitonomy\Bundle\CoreBundle\Entity\User;

class SplashController extends Controller
{
    public function loginAction(Request $request)
    {
        if ($this->isAuthenticated()) {
            return $this->redirect($this->generateUrl('project_list'));
        }

        $form = $this->get('form.factory')->createNamed('', 'login');

        $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        if ($error) {
            $this->setFlash('error', $this->trans('error.form_invalid', array(), 'login'));
            $request->getSession()->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('GitonomyWebsiteBundle:Splash:login.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function loginCheckAction()
    {
        throw new \RuntimeException('You should not be able to reach this action');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You should not be able to reach this action');
    }

    public function registerAction()
    {
        if (!$this->get('gitonomy_core.config')->get('open_registration')) {
            throw $this->createNotFoundException('Registration disabled');
        }
        $form = $this->createForm('register', new User());

        return $this->render('GitonomyWebsiteBundle:Splash:register.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function postRegisterAction(Request $request)
    {
        if (!$this->get('gitonomy_core.config')->get('open_registration')) {
            throw $this->createNotFoundException('Registration disabled');
        }

        $user = new User();
        $form = $this->createForm('register', $user);

        if ($form->bind($request)->isValid()) {
            $this->persistEntity($user);
            $this->setFlash('success', $this->trans('notice.success', array(), 'register'));

            return $this->redirect($this->generateUrl('splash_login'));
        }

        $this->setFlash('error', $this->trans('error.form_invalid', array(), 'register'));

        return $this->render('GitonomyWebsiteBundle:Splash:register.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function forgotPasswordAction()
    {
        $form = $this->createForm('email');

        return $this->render('GitonomyWebsiteBundle:Splash:forgotPassword.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function postForgotPasswordAction(Request $request)
    {
        $form = $this->createForm('email');
        $form->bind($request);
        $email = $form->getData();

        try {
            $user  = $this->getRepository('GitonomyCoreBundle:User')->findOneByEmail($email);
            $token = $this->getRepository('GitonomyCoreBundle:UserForgotPassword')->findOneByUser($user);
            $token->setToken();
            $this->persistEntity($token);

            $this->mail($user, 'GitonomyWebsiteBundle:Mail:forgotPassword.mail.twig', array('user' => $user, 'email' => $email, 'token' => $token));
        } catch (NoResultException $e) {}

        $this->setFlash('success', $this->trans('notice.mail_sent', array(), 'forgot_password'));

        return $this->redirect($this->generateUrl('splash_login'));
    }

    public function changePasswordAction($username, $token)
    {
        list($user, $forgotPassword) = $this->validateForgotPasswordToken($username, $token);
        $form                        = $this->createForm('change_password', $user);

        return $this->render('GitonomyWebsiteBundle:Splash:changePassword.html.twig', array(
            'form'     => $form->createView(),
            'user'     => $user,
            'username' => $username,
            'token'    => $token
        ));
    }

    public function postChangePasswordAction(Request $request, $username, $token)
    {
        list($user, $forgotPassword) = $this->validateForgotPasswordToken($username, $token);
        $form                        = $this->createForm('change_password', $user);

        $form->bind($request);

        if ($form->isValid()) {
            $this->persistEntity($user);
            $this->setFlash('success', $this->trans('notice.password_changed', array(), 'forgot_password'));

            return $this->redirect($this->generateUrl('splash_login'));
        }

        return $this->render('GitonomyWebsiteBundle:Splash:changePassword.html.twig', array(
            'form'     => $form->createView(),
            'user'     => $user,
            'username' => $username,
            'token'    => $token
        ));
    }

    protected function validateForgotPasswordToken($username, $token)
    {
        $user = $this->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException('No user with username '.$username);
        }

        $forgotPassword = $this->getRepository('GitonomyCoreBundle:UserForgotPassword')->findOneByUser($user);
        try {
            $forgotPassword->validateToken($token);
        } catch (\Exception $e) {
            throw $this->createNotFoundException('This token is invalid', $e);
        }

        return array($user, $forgotPassword);
    }

    public function activateEmailAction($token)
    {
        $email = $this->getRepository('GitonomyCoreBundle:Email')->findOneByActivationToken($token);

        if (!$email) {
            throw $this->createNotFoundException(sprintf('Token "%s" not found', $token));
        }

        $email->validateActivationToken($token);
        $this->flush();

        return $this->render('GitonomyWebsiteBundle:Splash:activateEmail.html.twig', array(
            'email' => $email,
        ));
    }
}
