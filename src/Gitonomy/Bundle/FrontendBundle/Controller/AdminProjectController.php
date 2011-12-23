<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Gitonomy\Bundle\CoreBundle\Entity\Role;
use Gitonomy\Bundle\FrontendBundle\Form\Role\RoleType;
use Gitonomy\Bundle\CoreBundle\Entity\Repository;
use Gitonomy\Bundle\CoreBundle\EventListener\GitonomyEvents;
use Gitonomy\Bundle\CoreBundle\EventListener\Event\ProjectCreateEvent;

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
        $this->get('event_dispatcher')->dispatch(GitonomyEvents::PROJECT_CREATE, new ProjectCreateEvent($object));
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
