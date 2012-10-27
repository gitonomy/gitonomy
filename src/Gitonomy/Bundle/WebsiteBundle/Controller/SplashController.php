<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SplashController extends Controller
{
    public function loginAction()
    {
        $form = $this->get('form.factory')->createBuilder()
            ->add('username', 'text')
            ->add('password', 'password')
            ->getForm()
        ;

        return $this->render('GitonomyWebsiteBundle:Splash:login.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function registerAction()
    {
        $form = $this->get('form.factory')->createBuilder()
            ->add('fullname', 'text')
            ->add('email', 'email')
            ->add('timezone', 'timezone')
            ->add('password', 'repeated', array('type' => 'password'))
            ->getForm()
        ;

        return $this->render('GitonomyWebsiteBundle:Splash:register.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
