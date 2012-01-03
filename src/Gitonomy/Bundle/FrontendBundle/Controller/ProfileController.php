<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;
use Gitonomy\Bundle\CoreBundle\Entity\User;

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
        $this->assertPermission('AUTHENTICATED');
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

    public function changeUsernameAction()
    {
        $this->assertPermission('AUTHENTICATED');

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

    public function emailsAction()
    {
        $this->assertPermission('AUTHENTICATED');

        return $this->render('GitonomyFrontendBundle:Profile:emails.html.twig');
    }

    /**
     * List SSH keys and form for adding a new one.
     */
    public function sshKeysAction()
    {
        $this->assertPermission('AUTHENTICATED');

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
        $this->assertPermission('AUTHENTICATED');

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
        $this->assertPermission('AUTHENTICATED');

        $userSshKey = new UserSshKey();
        $userSshKey->setUser($this->getUser());
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
//        $this->assertPermission('IS_AUTHENTICATED_ANONYMOUSLY');

        $em   = $this->getDoctrine()->getManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

        if (!$user) {
            throw $this->createNotFoundException(sprintf('User "%s" not found!', $username));
        }

        if ($user->isActived()) {
            throw $this->createException(sprintf('User "%s" is already actived!', $username));
        }

        if ($user->getActivationToken() !== $token) {
            throw $this->createException('Bad activation token!');
        }

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
