<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Gitonomy\Bundle\CoreBundle\Entity\Role;
use Gitonomy\Bundle\FrontendBundle\Form\Role\RoleType;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;

use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;
use Gitonomy\Bundle\CoreBundle\Entity\ProjectGitAccess;

/**
 * Project administration controller.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class AdminProjectController extends BaseAdminController
{
    /**
     * Displays list of Git accesses to the project.
     */
    public function gitAccessesAction($id)
    {
        $this->assertPermission('PROJECT_EDIT');
        $project = $this->getProject($id);

        $gitAccess = new ProjectGitAccess();
        $form      = $this->createForm('project_git_access', $gitAccess);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $gitAccess->setProject($project);
                $em = $this->getDoctrine()->getManager();
                $em->persist($gitAccess);
                $em->flush();

                $this->get('session')->setFlash('success', 'New git access saved!');

                return $this->redirect($this->generateUrl('gitonomyfrontend_adminproject_gitAccesses', array('id' => $project->getId())));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminProject:gitAccesses.html.twig', array(
            'object' => $project,
            'form'   => $form->createView()
        ));
    }

    public function gitAccessDeleteAction($gitAccessId)
    {
        $this->assertPermission('PROJECT_EDIT');

        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:ProjectGitAccess');

        if (!$gitAccess = $repository->find($gitAccessId)) {
            throw $this->createNotFoundException(sprintf('No Git access found with id "%d".', $gitAccessId));
        }

        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->remove($gitAccess);
                $em->flush();

                $this->get('session')->setFlash('success', 'Access deleted');

                return $this->redirect($this->generateUrl($this->getRouteName('gitAccesses'), array(
                    'id' => $gitAccess->getProject()->getId()
                )));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminProject:gitAccessDelete.html.twig', array(
            'object'       => $gitAccess->getProject(),
            'gitAccess'    => $gitAccess,
            'form'         => $form->createView(),
        ));
    }

    /**
     * Action to create a new user role project for an user.
     */
    public function userRolesAction($id)
    {
        $this->assertPermission('PROJECT_EDIT');
        $project = $this->getProject($id);

        $userRoleProject = new UserRoleProject();
        $em              = $this->getDoctrine()->getEntityManager();
        $repository      = $em->getRepository('GitonomyCoreBundle:User');
        $usedUsers       = $repository->findByProject($project);
        $totalUsers      = $repository->findAll();
        $request         = $this->getRequest();

        $form = $this->createForm('adminuserroleproject', $userRoleProject, array(
            'usedUsers' => $usedUsers,
            'from'      => 'project',
        ));

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $userRoleProject->setProject($project);
                $em->persist($userRoleProject);
                $em->flush();

                $this->get('session')->setFlash('success', sprintf('%s.', $userRoleProject->__toString()));

                return $this->redirect($this->generateUrl($this->getRouteName('userRoles'), array('id' => $project->getId())));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminProject:userRoles.html.twig', array(
            'object'      => $project,
            'form'        => $form->createView(),
            'displayForm' => $totalUsers > $usedUsers,
        ));
    }

    public function userRoleDeleteAction($userRoleId)
    {
        $this->assertPermission('PROJECT_EDIT');

        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:UserRoleProject');

        if (!$userRole = $repository->find($userRoleId)) {
            throw $this->createNotFoundException(sprintf('No UserRole found with id "%d".', $userRoleId));
        }

        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->remove($userRole);
                $em->flush();

                $this->get('session')->setFlash('success', sprintf('Role "%s" deleted.', $userRole->__toString()));

                return $this->redirect($this->generateUrl($this->getRouteName('userRoles'), array(
                    'id' => $userRole->getProject()->getId()
                )));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminProject:userRoleDelete.html.twig', array(
            'object'       => $userRole->getProject(),
            'userRole'     => $userRole,
            'form'         => $form->createView(),
        ));
    }

    /**
     * @see BaseAdminController:postCreate
     */
    protected function postCreate($object)
    {
        $this->get('event_dispatcher')->dispatch(GitonomyEvents::PROJECT_CREATE, new ProjectEvent($object));
        $em = $this->get('doctrine')->getManager();

        $em->persist($object);
        $em->flush();
    }

    /**
     * @see BaseAdminController:preDelete
     */
    protected function preDelete($object)
    {
        $this->get('event_dispatcher')->dispatch(GitonomyEvents::PROJECT_DELETE, new ProjectEvent($object));
    }

    /**
     * @see BaseAdminController::listAction
     */
    public function listAction()
    {
        $this->assertPermission(array('PROJECT_ADMIN'));

        return parent::listAction();
    }

    /**
     * @see BaseAdminController::createAction
     */
    public function createAction()
    {
        $this->assertPermission('PROJECT_CREATE');

        return parent::createAction();
    }

    /**
     * @see BaseAdminController::editAction
     */
    public function editAction($id)
    {
        $this->assertPermission('PROJECT_EDIT');

        return parent::editAction($id);
    }

    /**
     * @see BaseAdminController::deleteAction
     */
    public function deleteAction($id)
    {
        $this->assertPermission('PROJECT_DELETE');

        return parent::deleteAction($id);
    }

    /**
     * Returns the project or throws an exception if the project was not found.
     *
     * @return Gitonomy\Bundle\CoreBundle\Entity\Project
     */
    protected function getProject($id)
    {
        $project = $this->getRepository()->find($id);
        if (null === $project) {
            throw $this->createNotFoundException(sprintf("Project #%s not found", $id));
        }

        return $project;
    }

    /**
     * @see BaseAdminController:getRepository
     * @return Gitonomy\Bundle\CoreBundle\Repository\ProjectRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getEntityManager()->getRepository('GitonomyCoreBundle:Project');
    }
}
