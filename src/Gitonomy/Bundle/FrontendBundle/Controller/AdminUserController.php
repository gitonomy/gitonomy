<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;
use Gitonomy\Bundle\CoreBundle\Entity\Email;

/**
 * Controller for user actions.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */

class AdminUserController extends BaseAdminController
{
    public function listAction()
    {
        $this->assertPermission('USER_ADMIN');

        return parent::listAction();
    }

    public function createAction()
    {
        $this->assertPermission('USER_CREATE');

        return parent::createAction();
    }

    public function editAction($id, $vars = array())
    {
        $this->assertPermission('USER_EDIT');

        return parent::editAction($id);
    }

    /**
     * Action to create a new user role project for an user
     */
    public function userRolesAction($userId)
    {
        $this->assertPermission('USER_EDIT');

        if (!$user = $this->getRepository()->find($userId)) {
            throw new HttpException(404, sprintf('No %s found with id "%d".', $className, $id));
        }

        $userRoleProject = new UserRoleProject();
        $em              = $this->getDoctrine()->getEntityManager();
        $repository      = $em->getRepository('GitonomyCoreBundle:Project');
        $usedProjects    = $repository->findByUser($user);
        $totalProjects   = $repository->findAll();
        $request         = $this->getRequest();

        $form = $this->createForm('adminuserroleproject', $userRoleProject, array(
            'usedProjects' => $usedProjects,
            'from'         => 'user'
        ));

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $userRoleProject->setUser($user);
                $em->persist($userRoleProject);
                $em->flush();

                $this->get('session')->setFlash('success',
                    sprintf('%s.',
                        $userRoleProject->__toString()
                    )
                );

                return $this->redirect($this->generateUrl($this->getRouteName('edit'), array(
                    'id' => $user->getId()
                )));
            }
        }
        return $this->render('GitonomyFrontendBundle:AdminUser:projectroles.html.twig', array(
            'user'        => $user,
            'form'        => $form->createView(),
            'displayForm' => $totalProjects > $usedProjects,
        ));
    }

    public function deleteUserRoleAction($id)
    {
        $this->assertPermission('USER_EDIT');

        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:UserRoleProject');

        if (!$userRole = $repository->find($id)) {
            throw new HttpException(404, sprintf('No UserRole found with id "%d".', $id));
        }

        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->remove($userRole);
                $em->flush();

                $this->get('session')->setFlash('success',
                    sprintf('Role "%s" deleted.', $userRole->__toString())
                );

                return $this->redirect($this->generateUrl($this->getRouteName('edit'), array(
                    'id' => $userRole->getUser()->getId()
                )));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminRole:deleteUserRole.html.twig', array(
            'object' => $userRole,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Action to create an email for an user
     */
    public function emailsAction($userId)
    {
        $this->assertPermission('USER_EDIT');

        if (!$user = $this->getRepository()->find($userId)) {
            throw new HttpException(404, sprintf('No %s found with id "%d".', $className, $id));
        }

        $email   = new Email();
        $em      = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        $form = $this->createForm('useremail', $email);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $email->setUser($user);
                $em->persist($email);
                $em->flush();

                $this->get('session')->setFlash('success',
                    sprintf('%s.',
                        $email->__toString()
                    )
                );

                return $this->redirect($this->generateUrl($this->getRouteName('edit'), array(
                    'id' => $user->getId()
                )));
            }
        }
        return $this->render('GitonomyFrontendBundle:AdminUser:emails.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Action to delete an email for a user
     */
    public function deleteEmailAction($id)
    {
        $this->assertPermission('USER_EDIT');

        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:Email');

        if (!$email = $repository->find($id)) {
            throw new HttpException(404, sprintf('No Email found with id "%d".', $id));
        }

        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->remove($email);
                $em->flush();

                $this->get('session')->setFlash('success',
                    sprintf('Email "%s" deleted.', $email->__toString())
                );

                return $this->redirect($this->generateUrl($this->getRouteName('edit'), array(
                    'id' => $email->getUser()->getId()
                )));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminUser:deleteEmail.html.twig', array(
            'object' => $email,
            'form'   => $form->createView(),
        ));
    }

    public function deleteAction($id)
    {
        $this->assertPermission('USER_DELETE');

        return parent::deleteAction($id);
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getEntityManager()->getRepository('GitonomyCoreBundle:User');
    }
}
