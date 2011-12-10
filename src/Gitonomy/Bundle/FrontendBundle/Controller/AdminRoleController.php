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

class AdminRoleController extends BaseAdminController
{
    protected function getRepository()
    {
        return $this->getDoctrine()->getEntityManager()->getRepository('GitonomyCoreBundle:Role');
    }

    public function listAction()
    {
        $this->isGranted(array('ROLE_CREATE', 'ROLE_EDIT', 'ROLE_DELETE'));

        return parent::listAction();
    }

    public function createAction()
    {
        $this->isGranted('ROLE_CREATE');

        return parent::createAction();
    }

    public function editAction($id)
    {
        $this->isGranted('ROLE_EDIT');

        return parent::editAction($id);
    }

    public function deleteAction($id)
    {
        $this->isGranted('ROLE_DELETE');

        return parent::deleteAction($id);
    }
}
