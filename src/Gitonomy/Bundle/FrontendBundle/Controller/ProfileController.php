<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Gitonomy\Bundle\FrontendBundle\Form;
use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller for user profile.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ProfileController extends BaseController
{
    /**
     * Edit global informations.
     */
    public function indexAction()
    {
        $this->assertPermission('ROLE_USER');
        $user = $this->getUser();

        $form = $this->createForm(new Form\UserInformationsType(), $user);

        $request = $this->getRequest();
        if ($request->getMethod() === 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
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
     * List SSH keys and form for adding a new one.
     */
    public function sshKeysAction()
    {
        $this->assertPermission('ROLE_USER');

        $form = $this->createForm(new Form\CreateSshKeyType());

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
        $this->assertPermission('ROLE_USER');

        $em = $this->getDoctrine()->getEntityManager();
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
        $this->assertPermission('ROLE_USER');

        $userSshKey = new UserSshKey();
        $userSshKey->setUser($this->getUser());
        $form = $this->createForm(new Form\CreateSshKeyType(), $userSshKey);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
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
}
