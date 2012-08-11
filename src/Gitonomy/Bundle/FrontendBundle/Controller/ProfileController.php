<?php

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
     * Edit global informations.
     */
    public function indexAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $form = $this->createForm('profile_informations', $user);

        $request = $this->getRequest();
        if ($request->getMethod() === 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->get('session')->setFlash('success', 'Profile updated!');

                return $this->redirect($this->generateUrl('gitonomyfrontend_profile_index'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Profile:index.html.twig', array(
            'form' => $form->createView()
        ));
    }

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

                $this->get('session')->setFlash('success', 'This new username is so cool!');

                return $this->redirect($this->generateUrl('gitonomyfrontend_profile_changeUsername'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Profile:changeUsername.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Manage emails.
     */
    public function emailsAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $user    = $this->getUser();
        $email   = $user->createEmail();
        $request = $this->getRequest();

        $form = $this->createForm('useremail', $email, array(
            'validation_groups' => 'profile',
        ));

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                try {
                    $em->getConnection()->beginTransaction();
                    $token = $email->createActivationToken();
                    $em->persist($user);
                    $em->flush();
                    $this->sendActivationMail($email, $token);
                    $em->commit();
                } catch (\Exception $e) {
                    throw $e;
                }
                $message = sprintf('Email "%s" added.', $email->getEmail());
                $this->get('session')->setFlash('success', $message);

                return $this->redirect($this->generateUrl('gitonomyfrontend_profile_emails'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Profile:emails.html.twig', array(
            'object' => $user,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Action to delete an email for a user from admin user
     */
    public function emailDeleteAction($emailId)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $email   = $this->findEmail($emailId);
        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($email);
                $em->flush();
                $message = sprintf('Email "%s" deleted.', $email->getEmail());
                $this->get('session')->setFlash('success', $message);

                return $this->redirect($this->generateUrl('gitonomyfrontend_profile_email_list'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Profile:deleteEmail.html.twig', array(
            'object' => $email,
            'form'   => $form->createView(),
        ));
    }

    public function emailSendActivationAction($emailId)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $email = $this->findEmail($emailId);
        $token = $email->createActivationToken();
        $this->sendActivationMail($email, $token);

        $message = sprintf('Activation mail for "%s" sent.', $email->getEmail());
        $this->get('session')->setFlash('success', $message);

        return $this->redirect($this->generateUrl('gitonomyfrontend_profile_email_list'));
    }

    /**
     * Action to make as default an email from admin user
     */
    public function emailDefaultAction($emailId)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $defaultEmail = $this->findEmail($emailId);
        $user         = $defaultEmail->getUser();
        $em           = $this->getDoctrine()->getEntityManager();

        if (!$defaultEmail->isActive()) {
            throw new \LogicException(sprintf('Email "%d" is not actived!', $defaultEmail->getId()));
        }
        foreach ($user->getEmails() as $email) {
            if ($email->isDefault()) {
                $email->setDefault(false);
            }
        }

        $defaultEmail->setDefault(true);
        $em->flush();
        $message = sprintf('Email "%s" now as default.', $email->getEmail());
        $this->get('session')->setFlash('success', $message);

        return $this->redirect($this->generateUrl('gitonomyfrontend_profile_email_list'));
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

                $this->get('session')->setFlash('success', 'Profile updated!');

                return $this->redirect($this->generateUrl('gitonomyfrontend_profile_index'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Profile:activate.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    protected function findEmail($emailId)
    {
        $user = $this->getUser();
        $em   = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('GitonomyCoreBundle:Email');
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
        $user->setPassword($encoder->encodePassword($user->getPassword(), $user->regenerateSalt()));

        $em = $this->getDoctrine()->getEntityManagerForClass('Gitonomy\Bundle\CoreBundle\Entity\User');
    }
}
