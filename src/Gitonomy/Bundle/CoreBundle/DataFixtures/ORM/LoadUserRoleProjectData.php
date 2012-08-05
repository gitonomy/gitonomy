<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;

/**
 * @author Julien DIDIER <julien@jdidier.net>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadUserRoleProjectData extends AbstractFixture implements OrderedFixtureInterface
{
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
        $leadUser = $manager->merge($this->getReference('user-lead'));
        $alice    = $manager->merge($this->getReference('user-alice'));
        $bob      = $manager->merge($this->getReference('user-bob'));
        $charlie  = $manager->merge($this->getReference('user-charlie'));

        $lead    = $manager->merge($this->getReference('role-lead-developer'));
        $dev     = $manager->merge($this->getReference('role-developer'));
        $visitor = $manager->merge($this->getReference('role-visitor'));

        $foobar = $manager->merge($this->getReference('project-foobar'));
        $barbaz = $manager->merge($this->getReference('project-barbaz'));

        $userRoleProjects = array(
            // foobar
            new UserRoleProject($leadUser, $foobar, $lead),
            new UserRoleProject($alice,    $foobar, $dev),
            new UserRoleProject($bob,      $foobar, $dev),
            new UserRoleProject($charlie,  $foobar, $visitor),

            // barbaz
            new UserRoleProject($alice,    $barbaz, $lead),
        );

        foreach ($userRoleProjects as $userRoleProject) {
            $manager->persist($userRoleProject);
        }

        $manager->flush();

        $this->manager = null;
   }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 4;
    }
}
