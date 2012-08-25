<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\ProjectGitAccess;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadProjectGitAccessData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Returns a verbose-less array with plan of project git accesses creation.
     */
    protected function getData()
    {
        return array(
            array('foobar', 'lead-developer',  '*', true, true,  true),
            array('foobar', 'developer',       '*', true, true,  false),
            array('foobar', 'visitor',         '*', true, false, false),
        );
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $foobar = $manager->merge($this->getReference('project-foobar'));

        $lead      = $manager->merge($this->getReference('role-lead-developer'));
        $developer = $manager->merge($this->getReference('role-developer'));
        $visitor   = $manager->merge($this->getReference('role-visitor'));

        $accesses = array(
            new ProjectGitAccess($foobar, $lead,      '*', true, true, true),
            new ProjectGitAccess($foobar, $developer, '*', true, true, false),
            new ProjectGitAccess($foobar, $visitor,   '*', true, false, false)
        );

        foreach ($accesses as $access) {
            $manager->persist($access);
        }

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 3;
    }
}
