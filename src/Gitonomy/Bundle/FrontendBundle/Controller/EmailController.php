<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Gitonomy\Bundle\CoreBundle\Entity\Email;
use Gitonomy\Bundle\CoreBundle\Entity\User;

class EmailController extends BaseController
{
    /**
     * Action to create an email from admin user
     */
    public function adminUserListAction($userId)
    {
        $this->assertPermission('USER_EDIT');

        if (!$user = $this->getDoctrine()->getRepository('GitonomyCoreBundle:User')->find($userId)) {
            throw new HttpException(404, sprintf('No %s found with id "%d".', $className, $id));
        }

        $email   = new Email();
        $request = $this->getRequest();
        $form    = $this->createForm('useremail', $email, array(
            'validation_groups' => 'admin',
        ));

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->saveEmail($user, $email);

                return $this->successAndRedirect($user, 'gitonomyfrontend_adminuser_edit', sprintf('Email "%s" added.', $email->__toString()));
            } else {
                return $this->failAndRedirect($user, 'gitonomyfrontend_adminuser_edit', 'Email you filled is not valid.');
            }
        }

        return $this->render('GitonomyFrontendBundle:Email:AdminUser/list.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Action to create an email from profile
     */
    public function profileListAction()
    {
        $this->assertPermission('AUTHENTICATED');

        $user    = $this->getUser();
        $email   = new Email();
        $request = $this->getRequest();

        $form = $this->createForm('useremail', $email, array(
            'validation_groups' => 'profile',
        ));

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->saveEmail($user, $email);

                return $this->successAndRedirect($user, 'gitonomyfrontend_profile_index', sprintf('Email "%s" added.', $email->__toString()));
            } else {
                return $this->failAndRedirect($user, 'gitonomyfrontend_profile_index', 'Email you filled is not valid.');
            }
        }

        return $this->render('GitonomyFrontendBundle:Email:Profile/list.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Action to make as default an email from admin user
     */
    public function adminUserDefaultAction($id)
    {
        $this->assertPermission('USER_EDIT');

        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:Email');

        if (!$email = $repository->find($id)) {
            throw new HttpException(404, sprintf('No Email found with id "%d".', $id));
        }

        $this->setDefaultEmail($email);

        return $this->successAndRedirect($user, 'gitonomyfrontend_adminuser_edit', sprintf('Email "%s" now as default.', $email->__toString()));
    }

    /**
     * Action to make as default an email from profile
     */
    public function profileDefaultAction($id)
    {
        $this->assertPermission('AUTHENTICATED');

        $user       = $this->getUser();
        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:Email');

        if (!$email = $repository->findOneBy(array('id' => $id, 'user' => $user))) {
            throw new HttpException(404, sprintf('No Email found with id "%d".', $id));
        }

        $this->setDefaultEmail($email);

        return $this->successAndRedirect($user, 'gitonomyfrontend_profile_index', sprintf('Email "%s" now as default.', $email->__toString()));
    }

    /**
     * Action to delete an email for a user from admin user
     */
    public function adminUserDeleteAction($id)
    {
        $this->assertPermission('USER_EDIT');

        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:Email');

        if (!$email = $repository->find($id)) {
            throw $this->createNotFoundException(sprintf('No Email found with id "%d".', $id));
        }

        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->deleteEmail($email);

                return $this->successAndRedirect($email->getUser(), 'gitonomyfrontend_adminuser_edit', sprintf('Email "%s" deleted.', $email->__toString()));
            }
        }

        return $this->render('GitonomyFrontendBundle:Email:AdminUser/delete.html.twig', array(
            'object' => $email,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Action to delete an email for a user from profile
     */
    public function profileDeleteAction($id)
    {
        $this->assertPermission('AUTHENTICATED');

        $user       = $this->getUser();
        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:Email');

        if (!$email = $repository->findOneBy(array('id' => $id, 'user' => $user))) {
            throw new HttpException(404, sprintf('No Email found with id "%d".', $id));
        }

        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->deleteEmail($email);

                return $this->successAndRedirect($email->getUser(), 'gitonomyfrontend_profile_index', sprintf('Email "%s" deleted.', $email->__toString()));
            }
        }

        return $this->render('GitonomyFrontendBundle:Email:Profile/delete.html.twig', array(
            'object' => $email,
            'form'   => $form->createView(),
        ));
    }

    protected function saveEmail(User $user, Email $email)
    {
        $em = $this->getDoctrine()->getEntityManager();
        try {
            $em->getConnection()->beginTransaction();
            $email->setUser($user);
            $email->generateActivationHash();
            $em->persist($email);
            $mailer = $this->get('gitonomy_frontend.mailer');
            $mailer->send(
                $mailer->renderMessage('GitonomyFrontendBundle:Mail:activateEmail.mail.twig', array('email' => $email)),
                null,
                $email->getEmail()
            );
            $em->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function failAndRedirect(User $user, $route, $message)
    {
        $this->get('session')->setFlash('warning', $message);

        return $this->redirect($this->generateUrl($route, array(
            'id' => $user->getId()
        )));
    }

    protected function successAndRedirect(User $user, $route, $message)
    {
        $this->get('session')->setFlash('success', $message);

        return $this->redirect($this->generateUrl($route, array(
            'id' => $user->getId()
        )));
    }

    protected function setDefaultEmail(Email $defaultEmail)
    {
        if (!$defaultEmail->isActived()) {
            throw new \LogicException(sprintf('Email "%s" cannot be set as default : email is not validated yet!', $email->__toString()));
        }

        $em   = $this->getDoctrine()->getEntityManager();
        $user = $defaultEmail->getUser();

        foreach ($user->getEmails() as $email) {
            if ($email->isDefault()) {
                $email->setIsDefault(false);
            }
        }

        $defaultEmail->setIsDefault(true);
        $em->flush();
    }

    protected function deleteEmail(Email $email)
    {
        if ($email->isDefault()) {
            throw new \LogicException(sprintf('Email "%s" cannot be deleted : email is default email!', $email->__toString()));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($email);
        $em->flush();
    }
}
