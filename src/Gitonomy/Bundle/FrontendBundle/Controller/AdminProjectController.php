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
class AdminProjectController extends BaseAdminController
{
    protected function getRepository()
    {
        return $this->getDoctrine()->getEntityManager()->getRepository('GitonomyCoreBundle:Project');
    }

    public function listAction()
    {
        $this->assertPermission(array('PROJECT_CREATE', 'PROJECT_EDIT', 'PROJECT_DELETE'));

        return parent::listAction();
    }

    public function createAction()
    {
        $this->assertPermission('PROJECT_CREATE');

        return parent::createAction();
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
