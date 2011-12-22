<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;

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
    public function projectRolesAction($userId)
    {
        $this->assertPermission('USER_EDIT');

        if (!$user = $this->getRepository()->find($userId)) {
            throw new HttpException(404, sprintf('No %s found with id "%d".', $className, $id));
        }

        $userRoleProject = new UserRoleProject();
        $em              = $this->getDoctrine()->getEntityManager();
        $usedProjects    = $em->getRepository('GitonomyCoreBundle:Project')->findByUser($user);
        $request         = $this->getRequest();

        $form = $this->createForm('adminuserroleproject', $userRoleProject, array(
            'usedProjects' => $usedProjects
        ));

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $userRoleProject->setUser($user);
                $em = $this->getDoctrine()->getEntityManager();
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
            'user'         => $user,
            'form'         => $form->createView(),
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
