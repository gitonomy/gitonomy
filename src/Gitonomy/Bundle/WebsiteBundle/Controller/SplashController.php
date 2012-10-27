<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

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
        if ($this->isAuthenticated()) {
            return $this->redirect($this->generateUrl('project_list'));
        }

        $form = $this->createForm('user_registration');

        return $this->render('GitonomyWebsiteBundle:Splash:register.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
