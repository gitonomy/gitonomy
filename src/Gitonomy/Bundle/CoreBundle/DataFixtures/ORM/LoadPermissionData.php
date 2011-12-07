<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Permission;

/**
 * Loads the fixtures for user object.
 *
 * @author Julien DIDIER <julien@jdidier.net>
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
        $userCreate->setIsGlobal(true);
        $manager->persist($userCreate);
        $this->setReference('permission-usercreate', $userCreate);

        $userEdit = new Permission();
        $userEdit->setName('User edit');
        $userEdit->setPermission('USER_EDIT');
        $userEdit->setIsGlobal(true);
        $manager->persist($userEdit);
        $this->setReference('permission-useredit', $userEdit);

        $userDelete = new Permission();
        $userDelete->setName('User delete');
        $userDelete->setPermission('USER_DELETE');
        $userDelete->setIsGlobal(true);
        $manager->persist($userDelete);
        $this->setReference('permission-userdelete', $userDelete);

        $projectContribute = new Permission();
        $projectContribute->setName('Project contribute');
        $projectContribute->setPermission('PROJECT_CONTRIBUTE');
        $manager->persist($projectContribute);
        $this->setReference('permission-projectcontribute', $projectContribute);

        $projectCommit = new Permission();
        $projectCommit->setName('Project commit');
        $projectCommit->setPermission('PROJECT_COMMIT');
        $manager->persist($projectCommit);
        $this->setReference('permission-projectcommit', $projectCommit);

        $projectCreate = new Permission();
        $projectCreate->setName('Project create');
        $projectCreate->setPermission('PROJECT_CREATE');
        $projectCreate->setIsGlobal(true);
        $manager->persist($projectCreate);
        $this->setReference('permission-projectcreate', $projectCreate);

        $projectEdit = new Permission();
        $projectEdit->setName('Project edit');
        $projectEdit->setPermission('PROJECT_EDIT');
        $projectEdit->setIsGlobal(true);
        $manager->persist($projectEdit);
        $this->setReference('permission-projectedit', $projectEdit);

        $projectDelete = new Permission();
        $projectDelete->setName('Project delete');
        $projectDelete->setPermission('PROJECT_DELETE');
        $projectDelete->setIsGlobal(true);
        $manager->persist($projectDelete);
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
