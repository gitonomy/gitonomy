<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

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
            $request->getSession()->setFlash('error', $this->trans('error.form_invalid', array(), 'login'));
            $request->getSession()->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('GitonomyWebsiteBundle:Splash:login.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function loginCheckAction()
    {
    }

    public function logoutAction()
    {
    }

    public function registerAction()
    {
        $form = $this->createForm('register', new User());

        return $this->render('GitonomyWebsiteBundle:Splash:register.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function postRegisterAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('register', $user);

        $form->bind($request);

        if ($form->isValid()) {
            $this->persistEntity($user);
            $this->get('session')->setFlash('success', $this->trans('notice.success', array(), 'register'));

            return $this->redirect($this->generateUrl('splash_login'));
        }

        $this->get('session')->setFlash('error', $this->trans('error.form_invalid', array(), 'register'));

        return $this->render('GitonomyWebsiteBundle:Splash:register.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
