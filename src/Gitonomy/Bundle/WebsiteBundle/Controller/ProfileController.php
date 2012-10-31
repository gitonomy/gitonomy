<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;
use Gitonomy\Bundle\CoreBundle\Entity\Email;

class ProfileController extends Controller
{
    public function informationsAction()
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

                $this->setFlash('success', $this->trans('notice.profile_saved', array(), 'profile'));

                return $this->redirect($this->generateUrl('profile_informations'));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Profile:informations.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
            'form_email' => $this->createForm('profile_email', new Email($user))->createView(),
        ));
    }

    public function emailCreateAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $email = new Email($user);
        $form = $this->createForm('profile_email', $email);

        $request = $this->getRequest();
        if ($request->getMethod() === 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($email);
                $em->flush();

                $this->setFlash('success', $this->trans('notice.profile_email_saved', array(), 'profile'));

                return $this->redirect($this->generateUrl('profile_informations'));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Profile:informations.html.twig', array(
            'user' => $user,
            'form' => $this->createForm('profile_informations', $user)->createView(),
            'form_email' => $form->createView(),
        ));
    }

    /**
     * Action to delete an email for a user from admin user
     */
    public function emailDeleteAction($id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $email = $this->findEmail($id);

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($email);
        $em->flush();
        $this->setFlash('success', $this->trans('notice.email_deleted', array('%email%' => $email->getEmail()), 'profile'));

        return $this->redirect($this->generateUrl('profile_informations'));
    }

    /**
     * Action to make as default an email
     */
    public function emailDefaultAction($id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $defaultEmail = $this->findEmail($id);
        $user         = $defaultEmail->getUser();

        if (!$defaultEmail->isActive()) {
            throw new \LogicException(sprintf('Email "%d" is not activated!', $defaultEmail->getId()));
        }

        foreach ($user->getEmails() as $email) {
            if ($email->isDefault()) {
                $email->setDefault(false);
            }
        }

        $defaultEmail->setDefault(true);
        $em = $this->getDoctrine()->getEntityManager();
        $em->flush();
        $message = $this->trans('notice.email_as_default', array('%email%' => $email->getEmail()), 'profile');
        $this->get('session')->setFlash('success', $message);

        return $this->redirect($this->generateUrl('profile_informations'));
    }

    public function sshKeysAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm('profile_ssh_key');

        return $this->render('GitonomyWebsiteBundle:Profile:sshKeys.html.twig', array(
            'sshKeys' => $this->getUser()->getSshKeys(),
            'form'    => $form->createView()
        ));
    }

    protected function findEmail($id)
    {
        $user = $this->getUser();
        $repo = $this->getRepository('GitonomyCoreBundle:Email');
        if (!$email = $repo->findOneBy(array('user' => $user, 'id' => $id))) {
            throw $this->createNotFoundException(sprintf('No Email found with id "%d".', $id));
        }

        return $email;
    }

    /**
     * Delete an SSH key.
     *
     * @todo Add CSRF
     */
    public function deleteSshKeyAction($id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $userSshKey = $this->getRepository('GitonomyCoreBundle:UserSshKey')->find($id);

        if (!$userSshKey) {
            throw $this->createNotFoundException();
        }

        if (!$this->getUser()->equals($userSshKey->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $this->removeEntity($userSshKey);

        $message = $this->trans('notice.ssh_key_deleted', array('%title%' => $userSshKey->getTitle()), 'profile');
        $this->setFlash('success', $message);

        return $this->redirect($this->generateUrl('profile_sshKeys'));
    }

    /**
     * Submit action for a SSH key creation.
     */
    public function createSshKeyAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $userSshKey = new UserSshKey($user);
        $form       = $this->createForm('profile_ssh_key', $userSshKey);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->persistEntity($userSshKey);

                $message = $this->trans('notice.ssh_key_created', array('%title%' => $userSshKey->getTitle()), 'profile');
                $this->get('session')->setFlash('success', $message);

                return $this->redirect($this->generateUrl('profile_sshKeys'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Profile:sshKeys.html.twig', array(
            'sshKeys' => $user->getSshKeys(),
            'form'    => $form->createView()
        ));
    }
}
