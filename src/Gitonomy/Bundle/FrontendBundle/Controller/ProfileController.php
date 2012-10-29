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

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\Email;

/**
 * Controller for user profile.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 * @author Julien DIDIER <julien@jdidier.net>
 */
class ProfileController extends BaseController
{
    /**
     * Change the username.
     */
    public function changeUsernameAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $form = $this->createForm('change_username', $user);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user->markAllKeysAsUninstalled();
                $em->persist($user);
                $em->flush();

                $this->get('session')->setFlash('success', $this->trans('notice.usernamed_changed', array(), 'profile'));

                return $this->redirect($this->generateUrl('gitonomyfrontend_profile_changeUsername'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Profile:changeUsername.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function emailSendActivationAction($emailId)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $email = $this->findEmail($emailId);
        $token = $email->createActivationToken();
        $this->sendActivationMail($email, $token);

        $message = $this->trans('notice.activation_sent', array('%email%' => $email->getEmail()), 'profile');
        $message = sprintf('Activation mail for "%s" sent.', $email->getEmail());
        $this->get('session')->setFlash('success', $message);

        return $this->redirect($this->generateUrl('gitonomyfrontend_profile_email_list'));
    }

    /**
     * Change the password.
     */
    public function passwordAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        $em = $this->getDoctrine()->getManager();

        $session = $this->getUser();
        $em->detach($session);

        $user = $this->findUser($session->getId());
        $form = $this->createForm('profile_password', $user);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $this->get('session')->setFlash('success', 'Your new password was conscientiously saved!');

                return $this->redirect($this->generateUrl('gitonomyfrontend_profile_password'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Profile:password.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * List SSH keys and form for adding a new one.
     */
    public function sshKeysAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm('profile_ssh_key');

        return $this->render('GitonomyFrontendBundle:Profile:sshKeys.html.twig', array(
            'sshKeys' => $this->getUser()->getSshKeys(),
            'form'    => $form->createView()
        ));
    }

    /**
     * Delete an SSH key.
     *
     * @todo Add CSRF
     */
    public function deleteSshKeyAction($id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $em         = $this->getDoctrine()->getManager();
        $userSshKey = $em->getRepository('GitonomyCoreBundle:UserSshKey')->find($id);

        if (!$userSshKey) {
            throw $this->createNotFoundException();
        }

        if (!$this->getUser()->equals($userSshKey->getUser())) {
            throw new AccessDeniedException();
        }

        $em->remove($userSshKey);
        $em->flush();

        $message = $this->trans('notice.ssh_key_deleted', array('%title%' => $userSshKey->getTitle()), 'profile');
        $this->get('session')->setFlash('success', $message);

        return $this->redirect($this->generateUrl('gitonomyfrontend_profile_sshKeys'));
    }

    /**
     * Submit action for a SSH key creation.
     */
    public function createSshKeyAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $userSshKey = new UserSshKey($this->getUser());
        $form = $this->createForm('profile_ssh_key', $userSshKey);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($userSshKey);
                $em->flush();

                $message = $this->trans('notice.ssh_key_created', array('%title%' => $userSshKey->getTitle()), 'profile');
                $this->get('session')->setFlash('success', $message);

                return $this->redirect($this->generateUrl('gitonomyfrontend_profile_sshKeys'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Profile:sshKeys.html.twig', array(
            'sshKeys' => $this->getUser()->getSshKeys(),
            'form'    => $form->createView()
        ));
    }

    /**
     * Validate activation for a profile
     */
    public function activateAction($username, $token)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new \LogicException('Permission denied', 403);
        }

        $em   = $this->getDoctrine()->getManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

        if (!$user) {
            throw $this->createNotFoundException(sprintf('User "%s" not found', $username));
        }

        $user->validateActivation($token);

        $form = $this->createForm('change_password', $user);

        $request = $this->getRequest();
        if ($request->getMethod() === 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->encodePassword($user);
                $user->removeActivationToken();
                $em->flush();

                $this->get('session')->setFlash('success', $this->trans('notice.password_updated', array(), 'profile'));

                return $this->redirect($this->generateUrl('gitonomyfrontend_profile_index'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Profile:activate.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    protected function findUser($userId)
    {
        $repo = $this->getRepository('GitonomyCoreBundle:User');
        if (!$user = $repo->find($userId)) {
            throw $this->createNotFoundException(sprintf('No User found with id "%d".', $userId));
        }

        return $user;
    }

    protected function findEmail($emailId)
    {
        $user = $this->getUser();
        $repo = $this->getRepository('GitonomyCoreBundle:Email');
        if (!$email = $repo->findOneBy(array('user' => $user, 'id' => $emailId))) {
            throw $this->createNotFoundException(sprintf('No Email found with id "%d".', $emailId));
        }

        return $email;
    }

    protected function sendActivationMail(Email $email, $token)
    {
        $this->get('gitonomy_frontend.mailer')->sendMessage('GitonomyFrontendBundle:Email:activateEmail.mail.twig', array(
            'email' => $email,
            'token' => $token
        ),
            $email->getEmail()
        );
    }

    /**
     * Encode the password of a user and save it.
     *
     * @param Gitonomy\Bundle\CoreBundle\Entity\User $user A user to register
     */
    protected function encodePassword(User $user)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);

        $em = $this->getDoctrine()->getEntityManagerForClass('Gitonomy\Bundle\CoreBundle\Entity\User');
    }
}
