<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

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
    public function load(ObjectManager $manager)
    {
        $roleAdmin = new Role();
        $roleAdmin->setName('Administrator');
        $roleAdmin->setDescription('Master of the application');
        $roleAdmin->setIsGlobal(true);
        $roleAdmin->addPermission($manager->merge($this->getReference('permission-ROLE_ADMIN')));
        $manager->persist($roleAdmin);
        $this->setReference('role-admin', $roleAdmin);

        $roleLeadDev = new Role();
        $roleLeadDev->setName('Lead developer');
        $roleLeadDev->setDescription('Merge leader');
        $roleLeadDev->setIsGlobal(false);
        $roleLeadDev->addPermission($manager->merge($this->getReference('permission-PROJECT_CONTRIBUTE')));
        $manager->persist($roleLeadDev);
        $this->setReference('role-lead-developer', $roleLeadDev);

        $roleVisitor = new Role();
        $roleVisitor->setName('Visitor');
        $roleVisitor->setDescription('Read-only viewers');
        $roleVisitor->setIsGlobal(false);
        $roleVisitor->addPermission($manager->merge($this->getReference('permission-PROJECT_CONTRIBUTE')));
        $manager->persist($roleVisitor);
        $this->setReference('role-visitor', $roleVisitor);

        $roleDeveloper = new Role();
        $roleDeveloper->setName('Developer');
        $roleDeveloper->setDescription('No admin access to repositories');
        $roleDeveloper->setIsGlobal(false);
        $roleDeveloper->addPermission($manager->merge($this->getReference('permission-PROJECT_CONTRIBUTE')));
        $manager->persist($roleDeveloper);
        $this->setReference('role-developer', $roleDeveloper);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 210;
    }
}
