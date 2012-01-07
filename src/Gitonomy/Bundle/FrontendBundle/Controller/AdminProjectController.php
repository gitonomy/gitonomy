<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Gitonomy\Bundle\CoreBundle\Entity\Role;
use Gitonomy\Bundle\FrontendBundle\Form\Role\RoleType;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;

use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;

/**
 * Controller for repository actions.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class AdminProjectController extends BaseAdminController
{
    public function gitAccessesAction($id)
    {
        $this->assertPermission('PROJECT_EDIT');

        if (!$project = $this->getRepository()->find($id)) {
            throw $this->createNotFoundException("Project not found");
        }

        $form = $this->createForm('project_git_accesses', $project->getGitAccesses());
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($project);
                $em->flush();

                $this->get('session')->setFlash('success', 'Accesses saved');

                return $this->redirect($this->generateUrl($this->getRouteName('gitAccesses'), array(
                    'id' => $project->getId()
                )));
            }
        }
        return $this->render('GitonomyFrontendBundle:AdminProject:gitAccesses.html.twig', array(
            'object' => $project,
            'form'   => $form->createView()
        ));
    }

    /**
     * Action to create a new user role project for an user
     */
    public function userRolesAction($id)
    {
        $this->assertPermission('PROJECT_EDIT');

        if (!$project = $this->getRepository()->find($id)) {
            throw $this->createNotFoundException(sprintf('No %s found with id "%d".', $className, $id));
        }

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

                $this->get('session')->setFlash('success',
                    sprintf('%s.',
                        $userRoleProject->__toString()
                    )
                );

                return $this->redirect($this->generateUrl($this->getRouteName('userRoles'), array(
                    'id' => $project->getId()
                )));
            }
        }
        return $this->render('GitonomyFrontendBundle:AdminProject:userRoles.html.twig', array(
            'object'      => $project,
            'form'        => $form->createView(),
            'displayForm' => $totalUsers > $usedUsers,
        ));
    }

    public function deleteUserRoleAction($id)
    {
        $this->assertPermission('PROJECT_EDIT');

        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:UserRoleProject');

        if (!$userRole = $repository->find($id)) {
            throw $this->createNotFoundException(sprintf('No UserRole found with id "%d".', $id));
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

                return $this->redirect($this->generateUrl($this->getRouteName('userRoles'), array(
                    'id' => $userRole->getProject()->getId()
                )));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminProject:deleteUserRole.html.twig', array(
            'object'       => $userRole->getProject(),
            'userRole'     => $userRole,
            'form'         => $form->createView(),
        ));
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getEntityManager()->getRepository('GitonomyCoreBundle:Project');
    }

    public function listAction()
    {
        $this->assertPermission(array('PROJECT_ADMIN'));

        return parent::listAction();
    }

    public function createAction()
    {
        $this->assertPermission('PROJECT_CREATE');

        return parent::createAction();
    }

    protected function postCreate($object)
    {
        $this->get('event_dispatcher')->dispatch(GitonomyEvents::PROJECT_CREATE, new ProjectEvent($object));
        $em = $this->get('doctrine')->getManager();

        $em->persist($object);
        $em->flush();
    }

    protected function preDelete($object)
    {
        $this->get('event_dispatcher')->dispatch(GitonomyEvents::PROJECT_DELETE, new ProjectEvent($object));
    }

    public function editAction($id)
    {
        $this->assertPermission('PROJECT_EDIT');

        return parent::editAction($id);
    }

    public function deleteAction($id)
    {
        $this->assertPermission('PROJECT_DELETE');

        return parent::deleteAction($id);
    }
}
