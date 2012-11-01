<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Gitonomy\Bundle\CoreBundle\Entity\Role;
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
        $form = $this->createForm('administration_user', $user, array('validation_groups' => 'admin'));

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
        $form = $this->createForm('administration_user', $user, array('validation_groups' => 'admin'));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->flush();

                $this->setFlash('success', $this->trans('notice.user_updated', array(), 'administration'));

                return $this->redirect($this->generateUrl('administration_users'));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Administration:editUser.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }


    public function rolesAction()
    {
        $this->assertGranted('ROLE_ROLE_LIST');

        $roles = $this->getRepository('GitonomyCoreBundle:Role')->findAll();

        return $this->render('GitonomyWebsiteBundle:Administration:roles.html.twig', array(
            'roles' => $roles,
        ));
    }

    public function createRoleAction(Request $request)
    {
        $this->assertGranted('ROLE_ROLE_CREATE');

        $role = new Role();
        $form = $this->createForm('administration_role', $role, array('validation_groups' => 'admin'));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->persistEntity($user);

                $this->setFlash('success', $this->trans('notice.role_created', array(), 'administration'));

                return $this->redirect($this->generateUrl('administration_editRole', array('id' => $role->getId())));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Administration:createRole.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editRoleAction(Request $request, $id)
    {
        $this->assertGranted('ROLE_ROLE_EDIT');

        $role = $this->getRepository('GitonomyCoreBundle:Role')->find($id);
        $form = $this->createForm('administration_role', $role, array('validation_groups' => 'admin'));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->flush();

                $this->setFlash('success', $this->trans('notice.role_updated', array(), 'administration'));

                return $this->redirect($this->generateUrl('administration_roles'));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Administration:editRole.html.twig', array(
            'role' => $role,
            'form' => $form->createView(),
        ));
    }
}
