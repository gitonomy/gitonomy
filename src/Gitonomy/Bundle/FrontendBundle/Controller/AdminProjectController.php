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
    /**
     * Action to create a new user role project for an user
     */
    public function projectRolesAction($projectId)
    {
        $this->assertPermission('PROJECT_EDIT');

        if (!$project = $this->getRepository()->find($projectId)) {
            throw new HttpException(404, sprintf('No %s found with id "%d".', $className, $id));
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

                return $this->redirect($this->generateUrl($this->getRouteName('edit'), array(
                    'id' => $project->getId()
                )));
            }
        }
        return $this->render('GitonomyFrontendBundle:AdminProject:userroles.html.twig', array(
            'project'     => $project,
            'form'        => $form->createView(),
            'displayForm' => $totalUsers > $usedUsers,
        ));
    }

    public function deleteProjectRoleAction($id)
    {
        $this->assertPermission('PROJECT_EDIT');

        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:UserRoleProject');

        if (!$projectRole = $repository->find($id)) {
            throw new HttpException(404, sprintf('No ProjectRole found with id "%d".', $id));
        }

        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->remove($projectRole);
                $em->flush();

                $this->get('session')->setFlash('success',
                    sprintf('Role "%s" deleted.', $projectRole->__toString())
                );

                return $this->redirect($this->generateUrl($this->getRouteName('edit'), array(
                    'id' => $projectRole->getProject()->getId()
                )));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminRole:deleteProjectRole.html.twig', array(
            'object'       => $projectRole,
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
