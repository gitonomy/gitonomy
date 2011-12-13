<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\Role;
use Gitonomy\Bundle\FrontendBundle\Form\Role\RoleType;

/**
 * Controller for user actions.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */

class AdminUserController extends BaseAdminController
{
    public function mailAction($id)
    {
        if (!$user = $this->getRepository()->find($id)) {
            throw new HttpException(404, sprintf('No user found with id "%d".', $id));
        }
        // send a notification per mail
        $this->get('gitonomy_frontend.mail_sender')->user($user);


        $this->get('session')->setFlash('success',
            sprintf('Mail sent for user "%s".',
                $user->__toString()
            )
        );

        return $this->redirect($this->generateUrl($this->getRouteName('list')));
    }

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

    public function editAction($id)
    {
        $this->assertPermission('USER_EDIT');

        return parent::editAction($id);
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
