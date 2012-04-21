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
        $roleAdmin->addPermission($this->getReference('permission-USER_CREATE'));
        $roleAdmin->addPermission($this->getReference('permission-USER_EDIT'));
        $roleAdmin->addPermission($this->getReference('permission-USER_DELETE'));
        $roleAdmin->addPermission($this->getReference('permission-PROJECT_CREATE'));
        $roleAdmin->addPermission($this->getReference('permission-PROJECT_EDIT'));
        $roleAdmin->addPermission($this->getReference('permission-PROJECT_DELETE'));
        $roleAdmin->addPermission($this->getReference('permission-ROLE_CREATE'));
        $roleAdmin->addPermission($this->getReference('permission-ROLE_EDIT'));
        $roleAdmin->addPermission($this->getReference('permission-ROLE_DELETE'));
        $manager->persist($roleAdmin);
        $this->setReference('role-admin', $roleAdmin);

        $roleLeadDev = new Role();
        $roleLeadDev->setName('Lead developer');
        $roleLeadDev->setDescription('Merge leader');
        $roleLeadDev->setIsGlobal(false);
        $roleLeadDev->addPermission($this->getReference('permission-GIT_READ'));
        $roleLeadDev->addPermission($this->getReference('permission-GIT_WRITE'));
        $roleLeadDev->addPermission($this->getReference('permission-GIT_FORCE'));
        $roleLeadDev->addPermission($this->getReference('permission-GIT_DELETE'));
        $manager->persist($roleLeadDev);
        $this->setReference('role-lead-developer', $roleLeadDev);

        $roleProjectManager = new Role();
        $roleProjectManager->setName('Project manager');
        $roleProjectManager->setDescription('Manage the project and the team');
        $roleProjectManager->setIsGlobal(false);
        $roleProjectManager->addPermission($this->getReference('permission-GIT_READ'));
        $manager->persist($roleProjectManager);
        $this->setReference('role-project-manager', $roleProjectManager);

        $roleDeveloper = new Role();
        $roleDeveloper->setName('Developer');
        $roleDeveloper->setDescription('Fork the project and commit to it');
        $roleDeveloper->setIsGlobal(false);
        $roleDeveloper->addPermission($this->getReference('permission-GIT_READ'));
        $roleDeveloper->addPermission($this->getReference('permission-GIT_WRITE'));
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
