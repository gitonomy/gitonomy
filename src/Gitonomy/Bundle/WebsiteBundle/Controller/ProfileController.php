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

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Gitonomy\Bundle\CoreBundle\Entity\Email;
use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;
use Gitonomy\Bundle\CoreBundle\Job\UpdateSshKeysJob;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    public function informationAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        return $this->render('GitonomyWebsiteBundle:Profile:information.html.twig', array(
            'user'       => $user,
            'form'       => $this->createForm('profile_information', $user)->createView(),
            'form_email' => $this->createForm('profile_email', new Email($user))->createView(),
            'token'      => $this->createToken('profile')
        ));
    }

    public function saveInformationAction(Request $request)
    {
        $form = $this->createForm('profile_information', $this->getUser());
        if ($form->bind($request)->isValid()) {
            $this->flush();
            $this->setFlash('success', $this->trans('notice.profile_saved', array(), 'profile_information'));

            return $this->redirect($this->generateUrl('profile_information'));
        }

        return $this->render('GitonomyWebsiteBundle:Profile:information.html.twig', array(
            'user'       => $this->getUser(),
            'form'       => $form->createView(),
            'form_email' => $this->createForm('profile_email', new Email($this->getUser()))->createView(),
            'token'      => $this->createToken('profile')
        ));
    }

    public function createEmailAction(Request $request)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $user  = $this->getUser();
        $email = new Email($user);
        $form  = $this->createForm('profile_email', $email);

        if ($form->bind($request)->isValid()) {
            $email->createActivationToken();

            $em = $this->getDoctrine()->getManager();
            $em->persist($email);
            $em->flush();

            $this->setFlash('success', $this->trans('notice.email_created', array(), 'profile_information'));

            return $this->redirect($this->generateUrl('profile_information'));
        }

        return $this->render('GitonomyWebsiteBundle:Profile:information.html.twig', array(
            'user'       => $user,
            'form'       => $this->createForm('profile_information', $user)->createView(),
            'form_email' => $form->createView(),
            'token'      => $this->createToken('profile')
        ));
    }

    public function deleteEmailAction(Request $request, $id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isTokenValid('profile', $request->query->get('token'))) {
            $this->setFlash('success', $this->trans('', array(), 'profile_information'));

            return $this->redirect($this->generateUrl('profile_information'));
        }

        $email = $this->findEmail($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($email);
        $em->flush();
        $this->setFlash('success', $this->trans('notice.email_deleted', array(), 'profile_information'));

        return $this->redirect($this->generateUrl('profile_information'));
    }

    public function defaultEmailAction(Request $request, $id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isTokenValid('profile', $request->query->get('token'))) {
            $this->setFlash('success', $this->trans('', array(), 'profile_information'));

            return $this->redirect($this->generateUrl('profile_information'));
        }

        $defaultEmail = $this->findEmail($id);
        $user         = $defaultEmail->getUser();

        if (!$defaultEmail->isActive()) {
            throw $this->createAccessDeniedException('Cannot activate a mail that was not activated');
        }

        $user->setDefaultEmail($defaultEmail);
        $this->persistEntity($defaultEmail);

        $this->setFlash('success', $this->trans('notice.default_email_changed', array(), 'profile_information'));

        return $this->redirect($this->generateUrl('profile_information'));
    }

    public function activateEmailAction(Request $request, $id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isTokenValid('profile', $request->query->get('token'))) {
            $this->setFlash('success', $this->trans('', array(), 'profile_information'));

            return $this->redirect($this->generateUrl('profile_information'));
        }

        $email = $this->findEmail($id);
        $token = $email->createActivationToken();
        $this->flush();

        $this->mail($email, 'GitonomyWebsiteBundle:Mail:activateEmail.mail.twig', array(
            'email' => $email,
            'token' => $token
        ));

        $this->setFlash('success', $this->trans('notice.activation_sent', array(), 'profile_information'));

        return $this->redirect($this->generateUrl('profile_information'));
    }

    /**
     * Change the password.
     */
    public function passwordAction(Request $request)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm('profile_password', $this->getUser());

        if ('POST' === $request->getMethod() && $form->bind($request)->isValid()) {
            $this->flush();
            $this->setFlash('success', 'Your new password was conscientiously saved!');

            return $this->redirect($this->generateUrl('profile_password'));
        }

        return $this->render('GitonomyWebsiteBundle:Profile:password.html.twig', array(
            'form' => $form->createView()
        ));
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

    /**
     * Delete an SSH key.
     *
     * @todo Add CSRF
     */
    public function deleteSshKeyAction(Request $request, $id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isTokenValid('ssh_key_delete', $request->query->get('token'))) {
            $this->setFlash('error', 'Invalid token');

            return $this->redirect($this->generateUrl('profile_sshKeys'));
        }

        $userSshKey = $this->getRepository('GitonomyCoreBundle:UserSshKey')->find($id);

        if (!$userSshKey) {
            throw $this->createNotFoundException();
        }

        if (!$this->getUser()->equals($userSshKey->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $this->removeEntity($userSshKey);

        $message = $this->trans('notice.ssh_key_deleted', array('%title%' => $userSshKey->getTitle()), 'profile_ssh');
        $this->setFlash('success', $message);

        return $this->redirect($this->generateUrl('profile_sshKeys'));
    }

    /**
     * Submit action for a SSH key creation.
     */
    public function createSshKeyAction(Request $request)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $userSshKey = new UserSshKey($user);
        $form       = $this->createForm('profile_ssh_key', $userSshKey);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod() && $form->bind($request)->isValid()) {
            $this->persistEntity($userSshKey);

            $message = $this->trans('notice.ssh_key_created', array('%title%' => $userSshKey->getTitle()), 'profile_ssh');
            $this->setFlash('success', $message);
            $this->get('gitonomy.job_manager')->delegate(new UpdateSshKeysJob());

            return $this->redirect($this->generateUrl('profile_sshKeys'));
        }

        return $this->render('GitonomyWebsiteBundle:Profile:sshKeys.html.twig', array(
            'sshKeys' => $user->getSshKeys(),
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
}
