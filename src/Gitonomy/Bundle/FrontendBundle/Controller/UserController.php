<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\FrontendBundle\Form\User\RegistrationType;

class UserController extends BaseController
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
        $form = $this->createForm(new RegistrationType(), $user);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->doRegister($user);

                $this->get('session')->setFlash('success', 'Your account was created!');

                return $this->redirect($this->generateUrl('gitonomyfrontend_main_homepage'));
            } else {
                $this->get('session')->setFlash('warning', 'Roger, we have a problem with your form');
            }
        }

        return $this->render('GitonomyFrontendBundle:User:register.html.twig', array(
            'form' => $form->createView()
        ));
    }

    protected function doRegister(User $user)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword($user->getPassword(), $user->regenerateSalt()));

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->flush();
    }
}