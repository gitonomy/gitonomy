<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Gitonomy\Bundle\CoreBundle\Entity\Role;
use Gitonomy\Bundle\FrontendBundle\Form\Role\RoleType;

/**
 * Controller for repository actions.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */

class AdminUserController extends BaseAdminController
{
    protected function getRepository()
    {
        return $this->getDoctrine()->getEntityManager()->getRepository('GitonomyCoreBundle:User');
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
}
