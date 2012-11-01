<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Gitonomy\Bundle\CoreBundle\Entity\User;

class AdministrationController extends Controller
{
    public function usersAction()
    {
        $this->assertGranted('ROLE_USER_LIST');

        $users = $this->getRepository('GitonomyCoreBundle:User')->findAll();

        return $this->render('GitonomyWebsiteBundle:Administration:users.html.twig', array(
            'users' => $users,
        ));
    }

    public function createUserAction(Request $request)
    {
        $this->assertGranted('ROLE_USER_CREATE');

        $user = new User();
        $form = $this->createForm('user', $user, array('validation_groups' => 'admin'));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->persistEntity($user);

                $this->setFlash('success', $this->trans('notice.user_created', array(), 'administration'));

                return $this->redirect($this->generateUrl('administration_editUser', array('id' => $user->getId())));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Administration:createUser.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editUserAction(Request $request, $id)
    {
        $this->assertGranted('ROLE_USER_EDIT');

        $user = $this->getRepository('GitonomyCoreBundle:User')->find($id);
        $form = $this->createForm('user', $user, array('validation_groups' => 'admin'));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->flush();

                $this->setFlash('success', $this->trans('notice.user_updated', array(), 'administration'));

                return $this->redirect($this->generateUrl('administration_editUser', array('id' => $user->getId())));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Administration:editUser.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    public function rolesAction()
    {
        return $this->render('GitonomyWebsiteBundle:Administration:roles.html.twig');
    }
}
