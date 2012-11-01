<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;
use Gitonomy\Bundle\CoreBundle\Entity\Email;

class ProfileController extends Controller
{
    public function informationsAction()
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        return $this->render('GitonomyWebsiteBundle:Profile:informations.html.twig', array(
            'user'       => $user,
            'form'       => $this->createForm('profile_informations', $user)->createView(),
            'form_email' => $this->createForm('profile_email', new Email($user))->createView(),
            'token'      => $this->createToken('profile')
        ));
    }

    public function saveInformationsAction(Request $request)
    {
        $form = $this->createForm('profile_informations', $this->getUser());
        if ($form->bind($request)->isValid()) {
            $this->flush();
            $this->setFlash('success', $this->trans('notice.profile_saved', array(), 'profile_informations'));

            return $this->redirect($this->generateUrl('profile_informations'));
        }

        return $this->render('GitonomyWebsiteBundle:Profile:informations.html.twig', array(
            'user'       => $user,
            'form'       => $form->createView(),
            'form_email' => $this->createForm('profile_email', new Email($user))->createView(),
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

            $this->setFlash('success', $this->trans('notice.email_created', array(), 'profile_informations'));

            return $this->redirect($this->generateUrl('profile_informations'));
        }

        return $this->render('GitonomyWebsiteBundle:Profile:informations.html.twig', array(
            'user'       => $user,
            'form'       => $this->createForm('profile_informations', $user)->createView(),
            'form_email' => $form->createView(),
        ));
    }

    public function deleteEmailAction(Request $request, $id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isTokenValid('profile', $request->query->get('token'))) {
            $this->setFlash('success', $this->trans('', array(), 'profile_informations'));

            return $this->redirect($this->generateUrl('profile_informations'));
        }

        $email = $this->findEmail($id);

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($email);
        $em->flush();
        $this->setFlash('success', $this->trans('notice.email_deleted', array(), 'profile_informations'));

        return $this->redirect($this->generateUrl('profile_informations'));
    }

    public function defaultEmailAction(Request $request, $id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isTokenValid('profile', $request->query->get('token'))) {
            $this->setFlash('success', $this->trans('', array(), 'profile_informations'));

            return $this->redirect($this->generateUrl('profile_informations'));
        }

        $defaultEmail = $this->findEmail($id);
        $user         = $defaultEmail->getUser();

        if (!$defaultEmail->isActive()) {
            throw $this->createAccessDeniedException('Cannot activate a mail that was not activated');
        }

        $user->setDefaultEmail($defaultEmail);
        $this->persistEntity($defaultEmail);

        $this->setFlash('success', $this->trans('notice.default_email_changed', array(), 'profile_informations'));

        return $this->redirect($this->generateUrl('profile_informations'));
    }

    public function activateEmailAction(Request $request, $id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isTokenValid('profile', $request->query->get('token'))) {
            $this->setFlash('success', $this->trans('', array(), 'profile_informations'));

            return $this->redirect($this->generateUrl('profile_informations'));
        }

        $email = $this->findEmail($id);
        $token = $email->createActivationToken();
        $this->flush();

        $this->mail($email, 'GitonomyWebsiteBundle:Mail:activateEmail.mail.twig', array(
            'email' => $email,
            'token' => $token
        ));

        $this->setFlash('success', $this->trans('notice.activation_sent', array(), 'profile_informations'));

        return $this->redirect($this->generateUrl('profile_informations'));
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

        $user = $this->getRepository('GitonomyCoreBundle:User')->find($session->getId());
        $form = $this->createForm('profile_password', $user);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->flush();

                $this->setFlash('success', 'Your new password was conscientiously saved!');

                return $this->redirect($this->generateUrl('profile_password'));
            }
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
    public function deleteSshKeyAction($id)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->isTokenValid('profile', $request->query->get('token'))) {
            $this->setFlash('success', $this->trans('', array(), 'profile_informations'));

            return $this->redirect($this->generateUrl('profile_informations'));
        }

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
        if (!$this->isTokenValid('profile', $request->query->get('token'))) {
            $this->setFlash('success', $this->trans('', array(), 'profile_informations'));

            return $this->redirect($this->generateUrl('profile_informations'));
        }

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
