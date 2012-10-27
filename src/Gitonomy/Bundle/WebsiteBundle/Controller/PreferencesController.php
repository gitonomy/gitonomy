<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

class PreferencesController extends Controller
{
    public function informationsAction()
    {
        $form = $this->get('form.factory')->createNamedBuilder('Preferences')
            ->add('fullname', 'text')
            ->add('timezone', 'timezone')
            ->getForm()
        ;

        return $this->render('GitonomyWebsiteBundle:Preferences:informations.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function sshKeysAction()
    {
        $form = $this->get('form.factory')->createNamedBuilder('Preferences')
            ->add('title', 'text')
            ->add('content', 'textarea')
            ->getForm()
        ;

        return $this->render('GitonomyWebsiteBundle:Preferences:sshKeys.html.twig', array(
            'createForm' => $form->createView()
        ));
    }
}
