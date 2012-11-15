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

use Symfony\Component\HttpFoundation\Request;

use Gitonomy\Bundle\CoreBundle\Entity\Role;
use Gitonomy\Bundle\CoreBundle\Entity\User;

class AdministrationController extends Controller
{
    public function usersAction()
    {
        $this->assertGranted('ROLE_ADMIN');

        $users = $this->getRepository('GitonomyCoreBundle:User')->findAll();

        return $this->render('GitonomyWebsiteBundle:Administration:users.html.twig', array(
            'users' => $users,
        ));
    }

    public function createUserAction(Request $request)
    {
        $this->assertGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm('administration_user', $user, array('validation_groups' => 'admin'));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->persistEntity($user);

                $this->setFlash('success', $this->trans('notice.created', array(), 'administration_user'));

                return $this->redirect($this->generateUrl('administration_editUser', array('id' => $user->getId())));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Administration:createUser.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editUserAction(Request $request, $id)
    {
        $this->assertGranted('ROLE_ADMIN');

        $user = $this->getRepository('GitonomyCoreBundle:User')->find($id);
        $form = $this->createForm('administration_user', $user, array('validation_groups' => 'admin'));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->flush();

                $this->setFlash('success', $this->trans('notice.updated', array(), 'administration_user'));

                return $this->redirect($this->generateUrl('administration_users'));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Administration:editUser.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    public function deleteUserAction(Request $request, $id)
    {
        $this->assertGranted('ROLE_ADMIN');

        $user = $this->getRepository('GitonomyCoreBundle:User')->find($id);

        $this->removeEntity($user);
        $this->setFlash('success', $this->trans('notice.deleted', array(), 'administration_user'));

        return $this->redirect($this->generateUrl('administration_users'));
    }

    public function rolesAction()
    {
        $this->assertGranted('ROLE_ADMIN');

        $roles = $this->getRepository('GitonomyCoreBundle:Role')->findAll();

        return $this->render('GitonomyWebsiteBundle:Administration:roles.html.twig', array(
            'roles' => $roles,
        ));
    }

    public function createRoleAction(Request $request)
    {
        $this->assertGranted('ROLE_ADMIN');

        $role = new Role();
        $form = $this->createForm('administration_role', $role, array('validation_groups' => 'admin'));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->persistEntity($role);

                $this->setFlash('success', $this->trans('notice.created', array(), 'administration_role'));

                return $this->redirect($this->generateUrl('administration_roles'));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Administration:createRole.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editRoleAction(Request $request, $id)
    {
        $this->assertGranted('ROLE_ADMIN');

        $role = $this->getRepository('GitonomyCoreBundle:Role')->find($id);
        $form = $this->createForm('administration_role', $role, array('validation_groups' => 'admin'));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->flush();

                $this->setFlash('success', $this->trans('notice.updated', array(), 'administration_role'));

                return $this->redirect($this->generateUrl('administration_roles'));
            }
        }

        return $this->render('GitonomyWebsiteBundle:Administration:editRole.html.twig', array(
            'role' => $role,
            'form' => $form->createView(),
        ));
    }

    public function deleteRoleAction($id)
    {
        $this->assertGranted('ROLE_ADMIN');

        $role = $this->getRepository('GitonomyCoreBundle:Role')->find($id);

        $this->removeEntity($role);
        $this->setFlash('success', $this->trans('notice.deleted', array(), 'administration_role'));

        return $this->redirect($this->generateUrl('administration_roles'));
    }
}
