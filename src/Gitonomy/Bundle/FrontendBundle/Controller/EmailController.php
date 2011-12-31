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
            $parameters = array('id' => $user->getId());
            if ($form->isValid()) {
                $this->saveEmail($user, $email);
                $message = sprintf('Email "%s" added.', $email->__toString());

                return $this->successAndRedirect($message, 'gitonomyfrontend_adminuser_edit', $parameters);
            } else {
                $message = 'Email you filled is not valid.';

                return $this->failAndRedirect($message, 'gitonomyfrontend_adminuser_edit', $parameters);
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
                $this->sendActivationMail($email);
                $message = sprintf('Email "%s" added.', $email->__toString());

                return $this->successAndRedirect($message, 'gitonomyfrontend_profile_index');
            } else {
                $message = 'Email you filled is not valid.';

                return $this->failAndRedirect($message, 'gitonomyfrontend_profile_index');
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

        $email      = $this->getEmail($id);
        $parameters = array('id' => $email->getUser()->getId());

        $this->setDefaultEmail($email);
        $message = sprintf('Email "%s" now as default.', $email->__toString());

        return $this->successAndRedirect($message, 'gitonomyfrontend_adminuser_edit', $parameters);
    }

    /**
     * Action to make as default an email from profile
     */
    public function profileDefaultAction($id)
    {
        $this->assertPermission('AUTHENTICATED');

        $user  = $this->getUser();
        $email = $this->getEmail($id, $user);

        if (!$email->isActived()) {
            throw new \LogicException(sprintf('Email "%s" cannot be set as default : email is not validated yet!', $email->__toString()));
        }

        $this->setDefaultEmail($email);
        $message = sprintf('Email "%s" now as default.', $email->__toString());

        return $this->successAndRedirect($message, 'gitonomyfrontend_profile_index');
    }

    /**
     * Action to delete an email for a user from admin user
     */
    public function adminUserDeleteAction($id)
    {
        $this->assertPermission('USER_EDIT');

        $email   = $this->getEmail($id);
        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->deleteEmail($email);
                $parameters = array('id' => $email->getUser()->getId());
                $message    = sprintf('Email "%s" deleted.', $email->__toString());

                return $this->successAndRedirect($message, 'gitonomyfrontend_adminuser_edit', $parameters);
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

        $user  = $this->getUser();
        $email = $this->getEmail($id, $user);

        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                if ($email->isDefault()) {
                    throw new \LogicException(sprintf('Email "%s" cannot be deleted : email is default email!', $email->__toString()));
                }

                $this->deleteEmail($email);
                $message = sprintf('Email "%s" deleted.', $email->__toString());

                return $this->successAndRedirect($message, 'gitonomyfrontend_profile_index');
            }
        }

        return $this->render('GitonomyFrontendBundle:Email:Profile/delete.html.twig', array(
            'object' => $email,
            'form'   => $form->createView(),
        ));
    }

    public function profileSendActivationAction($id)
    {
        $this->assertPermission('AUTHENTICATED');

        $user  = $this->getUser();
        $email = $this->getEmail($id, $user);

        $this->sendActivationMail($email);
        $message = sprintf('Activation mail for "%s" sent.', $email->__toString());

        return $this->successAndRedirect($message, 'gitonomyfrontend_profile_index');
    }

    public function adminUserSendActivationAction($id)
    {
        $this->assertPermission('USER_EDIT');

        $user  = $this->getUser();
        $email = $this->getEmail($id);

        $this->sendActivationMail($email);

        $message    = sprintf('Activation mail for "%s" sent.', $email->__toString());
        $parameters = array('id' => $email->getUser()->getId());

        return $this->successAndRedirect($message, 'gitonomyfrontend_adminuser_edit', $parameters);
    }

    public function activateAction($username, $hash)
    {
        $em   = $this->getDoctrine();
        $repo = $em->getRepository('GitonomyCoreBundle:Email');
        if (!$email = $repo->getEmailFromActivation($username, $hash)) {
            throw $this->createNotFoundException('There is no mail to activate with this link. Have you already activate it?');
        }

        $email->setActivation(null);
        $em->getEntityManager()->flush();

        $message = sprintf('Email "%s" actived.', $email->__toString());

        return $this->successAndRedirect($message, 'gitonomyfrontend_profile_index');
    }

    protected function getEmail($id, User $user = null)
    {
        $em         = $this->getDoctrine();
        $repository = $em->getRepository('GitonomyCoreBundle:Email');

        if (null === $user) {
            $email = $repository->find($id);
        } else {
            $email = $repository->findOneBy(array('id' => $id, 'user' => $user));
        }

        if (!$email) {
            throw $this->createNotFoundException(sprintf('No Email found with id "%d".', $id));
        }

        return $email;
    }

    protected function saveEmail(User $user, Email $email)
    {
        $em = $this->getDoctrine()->getEntityManager();
        try {
            $em->getConnection()->beginTransaction();
            $email->setUser($user);
            $user->addEmail($email);
            $em->persist($email);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function sendActivationMail(Email $email)
    {
        $this->get('gitonomy_frontend.mailer')->sendMessage('GitonomyFrontendBundle:Email:activateEmail.mail.twig',
            array('email' => $email),
            $email->getEmail()
        );
    }

    protected function failAndRedirect($message, $route, array $parameters = null)
    {
        $this->get('session')->setFlash('warning', $message);
        $parameters = (is_array($parameters) ? $parameters : array());

        return $this->redirect($this->generateUrl($route, $parameters));
    }

    protected function successAndRedirect($message, $route, array $parameters = null)
    {
        $this->get('session')->setFlash('success', $message);
        $parameters = (is_array($parameters) ? $parameters : array());

        return $this->redirect($this->generateUrl($route, $parameters));
    }

    protected function setDefaultEmail(Email $defaultEmail)
    {
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
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($email);
        $em->flush();
    }
}
