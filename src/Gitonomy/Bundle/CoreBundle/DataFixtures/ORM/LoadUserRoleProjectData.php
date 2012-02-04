<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;

/**
 * Loads the fixtures for user role object.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadUserRoleProjectData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Current instance of manager.
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $manager;

    /**
     * Returns a verbose-less array with plan of userRole creation.
     *
     * @return array An array where is element is ('user-XXX', 'role-XXX', ?'project-XXX')
     */
    protected function getData()
    {
        return array(
            // Foobar
            array('user-lead',    'role-lead-developer',  'project-foobar'),
            array('user-alice',   'role-developer',       'project-foobar'),
            array('user-bob',     'role-developer',       'project-foobar'),
            array('user-charlie', 'role-visitor',         'project-foobar'),
            // Barbaz
            array('user-alice', 'role-lead-developer', 'project-barbaz')
        );
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        foreach ($this->getData() as $row) {
            list($userReferenceName, $roleReferenceName, $projectReferenceName)  = $row;

            $userRole = $this->createUserRoleProject($userReferenceName, $roleReferenceName, $projectReferenceName);

            $manager->persist($userRole);
        }

        $manager->flush();

        $this->manager = null;
   }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 240;
    }

    /**
     * Prepares a UserRole object with reference names.
     *
     * @param type $userReferenceName
     * @param type $roleReferenceName
     * @param type $projectReferenceName
     * @return UserRole
     */
    protected function createUserRoleProject($userReferenceName, $roleReferenceName, $projectReferenceName)
    {
        $userRole = new UserRoleProject();
        $userRole->setProject($this->manager->merge($this->getReference($projectReferenceName)));
        $userRole->setUser($this->manager->merge($this->getReference($userReferenceName)));
        $userRole->setRole($this->manager->merge($this->getReference($roleReferenceName)));

        return $userRole;
    }
}
