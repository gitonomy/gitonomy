<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Role;

/**
 * Loads the fixtures for role object.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $roleAdmin = new Role();
        $roleAdmin->setName('Administrators');
        $roleAdmin->setDescription('Master of the application');
        $roleAdmin->addPermission($this->getReference('permission-usercreate'));
        $roleAdmin->addPermission($this->getReference('permission-useredit'));
        $roleAdmin->addPermission($this->getReference('permission-userdelete'));
        $roleAdmin->addPermission($this->getReference('permission-projectcreate'));
        $roleAdmin->addPermission($this->getReference('permission-projectedit'));
        $roleAdmin->addPermission($this->getReference('permission-projectdelete'));
        $manager->persist($roleAdmin);
        $this->setReference('role-admin', $roleAdmin);

        $roleLeadDev = new Role();
        $roleLeadDev->setName('Lead developers');
        $roleLeadDev->setDescription('Merge leader');
        $manager->persist($roleLeadDev);
        $this->setReference('role-lead-developer', $roleLeadDev);

        $roleProjectManager = new Role();
        $roleProjectManager->setName('Project manager');
        $roleProjectManager->setDescription('Manage the project and the team');
        $manager->persist($roleProjectManager);
        $this->setReference('role-project-manager', $roleProjectManager);

        $roleDeveloper = new Role();
        $roleDeveloper->setName('Developer');
        $roleDeveloper->setDescription('Fork the project and commit to it');
        $manager->persist($roleDeveloper);
        $this->setReference('role-developer', $roleDeveloper);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 30;
    }
}
