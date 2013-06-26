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
use Gitonomy\Bundle\CoreBundle\Entity\Email;

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

        if ('POST' === $request->getMethod() && $form->bind($request)->isValid()) {
            $this->persistEntity($user);

            $this->setFlash('success', $this->trans('notice.created', array(), 'administration_user'));

            return $this->redirect($this->generateUrl('administration_editUser', array('username' => $user->getUsername())));
        }

        return $this->render('GitonomyWebsiteBundle:Administration:createUser.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editUserAction(Request $request, $username)
    {
        $this->assertGranted('ROLE_ADMIN');
        $user = $this->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

        $form = $this->createForm('administration_user', $user, array('validation_groups' => 'admin'));

        if ('POST' === $request->getMethod() && $form->bind($request)->isValid()) {
            $this->flush();

            $this->setFlash('success', $this->trans('notice.updated', array(), 'administration_user'));

            return $this->redirect($this->generateUrl('administration_editUser', array('username' => $user->getUsername())));
        }

        return $this->render('GitonomyWebsiteBundle:Administration:editUser.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
            'mail_form' => $this->createForm('profile_email')->createView()
        ));
    }

    public function deleteUserAction($username)
    {
        $this->assertGranted('ROLE_ADMIN');

        $user = $this->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

        if ($user === $this->getUser()) {
            throw $this->createAccessDeniedException('Cannot delete self account');
        }

        $this->removeEntity($user);
        $this->setFlash('success', $this->trans('notice.deleted', array(), 'administration_user'));

        return $this->redirect($this->generateUrl('administration_users'));
    }

    public function emailCreateAction(Request $request, $username)
    {
        $this->assertGranted('ROLE_ADMIN');

        $user  = $this->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException(sprintf('No user with username "%s"', $username));
        }
        $email = new Email($user, null, true);
        $form  = $this->createForm('profile_email', $email);

        if ($form->bind($request)->isValid()) {
            $this->persistEntity($email);
            $this->setFlash('success', $this->trans('notice.email_created', array('%email%' => $email->getEmail()), 'administration_user'));

            return $this->redirect($this->generateUrl('administration_editUser', array('username' => $user->getUsername())));
        }

        return $this->render('GitonomyWebsiteBundle:Administration:editUser.html.twig', array(
            'user'      => $user,
            'form'      => $this->createForm('administration_user', $user, array('validation_groups' => 'admin'))->createView(),
            'mail_form' => $form->createView()
        ));
    }

    public function emailActionAction(Request $request, $id, $action)
    {
        $this->assertGranted('ROLE_ADMIN');

        $email = $this->getRepository('GitonomyCoreBundle:Email')->find($id);
        $user  = $email->getUser();

        if ($action === 'activate') {
            $email->activate();
            $success = $this->trans('notice.email_activated', array('%email%' => $email->getEmail()), 'administration_user');
        } elseif ($action === 'disactivate') {
            $email->disactivate();
            $success = $this->trans('notice.email_disactivated', array('%email%' => $email->getEmail()), 'administration_user');
        } elseif ($action === 'as_default') {
            $user->setDefaultEmail($email);
            $success = $this->trans('notice.email_as_default', array('%email%' => $email->getEmail()), 'administration_user');
        } elseif ($action == 'delete') {
            $this->removeEntity($email);
            $success = $this->trans('notice.email_deleted', array('%email%' => $email->getEmail()), 'administration_user');
        } else {
            throw $this->createNotFoundException(sprintf('No action %s on a mail in administration controller', $action));
        }

        $this->flush();
        $this->setFlash('success', $success);

        return $this->redirect($this->generateUrl('administration_editUser', array('username' => $user->getUsername())));
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

        if ('POST' === $request->getMethod() && $form->bind($request)->isValid()) {
            $this->persistEntity($role);

            $this->setFlash('success', $this->trans('notice.created', array(), 'administration_role'));

            return $this->redirect($this->generateUrl('administration_roles'));
        }

        return $this->render('GitonomyWebsiteBundle:Administration:createRole.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editRoleAction(Request $request, $id)
    {
        $this->assertGranted('ROLE_ADMIN');

        $role = $this->getRepository('GitonomyCoreBundle:Role')->find($id);
        $form = $this->createForm('administration_role', $role, array(
            'is_global' => $role->isGlobal(),
            'validation_groups' => 'admin'
        ));

        if ('POST' === $request->getMethod() && $form->bind($request)->isValid()) {
            $this->flush();

            $this->setFlash('success', $this->trans('notice.updated', array(), 'administration_role'));

            return $this->redirect($this->generateUrl('administration_roles'));
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

    public function configAction(Request $request)
    {
        $this->assertGranted('ROLE_ADMIN');

        $config = $this->get('gitonomy_core.config');
        $form = $this->createForm('administration_config', $config->all());

        if ($request->getMethod() === 'POST' && $form->bind($request)->isValid()) {
            $config->merge($form->getData());

            $this->setFlash('success', $this->trans('notice.config_updated', array(), 'administration_config'));

            return $this->redirect($this->generateUrl('administration_config'));
        }

        return $this->render('GitonomyWebsiteBundle:Administration:config.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function versionAction()
    {
        $this->assertGranted('ROLE_ADMIN');

        $browser   = $this->container->get('buzz')->getBrowser('gitonomy');
        $changeLog = $browser->getChangeLog();
        $version   = $this->container->getParameter('gitonomy.version');

        return $this->render('GitonomyWebsiteBundle:Administration:version.html.twig', array(
            'changeLog' => $changeLog,
            'version'   => $version,
        ));
    }
}
