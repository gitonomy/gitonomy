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
        $this->assertGranted('ROLE_ROLE_LIST');

        return parent::listAction();
    }

    public function createAction()
    {
        $this->assertGranted('ROLE_ROLE_CREATE');

        return parent::createAction();
    }

    public function editAction($id)
    {
        $this->assertGranted('ROLE_ROLE_EDIT');

        return parent::editAction($id);
    }

    public function deleteAction($id)
    {
        $this->assertGranted('ROLE_ROLE_DELETE');

        return parent::deleteAction($id);
    }

    protected function createAdminForm($object, $options = array()) {
        $options['is_global'] = $object->getIsGlobal();

        return parent::createAdminForm($object, $options);
    }
}
