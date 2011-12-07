<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\FrontendBundle\Form\RegistrationType;

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
        }

        return $this->render('GitonomyFrontendBundle:User:register.html.twig', array(
            'form' => $form->createView()
        ));
    }
}