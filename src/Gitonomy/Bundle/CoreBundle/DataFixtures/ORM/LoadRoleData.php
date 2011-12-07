<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Role;

/**
 * Loads the fixtures for user object.
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
        $roleAdmin->setDescription('Role of administrators');
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
        $roleLeadDev->setDescription('Role of lead developers');
        $roleLeadDev->addPermission($this->getReference('permission-usercreate'));
        $roleLeadDev->addPermission($this->getReference('permission-projectcreate'));
        $manager->persist($roleLeadDev);
        $this->setReference('role-lead', $roleLeadDev);

        $roleProjectManager = new Role();
        $roleProjectManager->setName('Project managers');
        $roleProjectManager->setDescription('Role of project managers');
        $roleProjectManager->addPermission($this->getReference('permission-usercreate'));
        $roleProjectManager->addPermission($this->getReference('permission-projectcreate'));
        $manager->persist($roleProjectManager);
        $this->setReference('permission-userdelete', $roleProjectManager);

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
