<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * Loads the fixtures for user object.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadPermissionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $userCreate = new Permission();
        $userCreate->setName('User create');
        $userCreate->setPermission('USER_CREATE');
        $this->setReference('permission-usercreate', $userCreate);

        $userEdit = new Permission();
        $userEdit->setName('User edit');
        $userEdit->setPermission('USER_EDIT');
        $this->setReference('permission-useredit', $userEdit);

        $userDelete = new Permission();
        $userDelete->setName('User delete');
        $userDelete->setPermission('USER_DELETE');
        $this->setReference('permission-userdelete', $userDelete);

        $projectContribute = new Permission();
        $projectContribute->setName('Project contribute');
        $projectContribute->setPermission('PROJECT_CONTRIBUTE');
        $this->setReference('permission-projectcontribute', $projectContribute);

        $projectContribute = new Permission();
        $projectContribute->setName('Project commit');
        $projectContribute->setPermission('PROJECT_COMMIT');
        $this->setReference('permission-projectcommit', $projectCommit);

        $projectEdit = new Permission();
        $projectEdit->setName('Project edit');
        $projectEdit->setPermission('PROJECT_EDIT');
        $this->setReference('permission-projectedit', $projectEdit);

        $projectDelete = new Permission();
        $projectDelete->setName('Project delete');
        $projectDelete->setPermission('PROJECT_DELETE');
        $this->setReference('permission-projectdelete', $projectDelete);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 20;
    }

}
