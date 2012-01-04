<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\ProjectGitAccess;

/**
 * Loads the fixtures for project git accesses.
 *
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
            array('foobar', 'project-manager', '*', true, false, false),
        );
    }

    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        foreach ($this->getData() as $row) {
            list($projectSlug, $roleSlug, $reference, $isRead, $isWrite, $isAdmin) = $row;

            $gitAccess = new ProjectGitAccess();
            $gitAccess->setProject($manager->merge($this->getReference('project-'.$projectSlug)));
            $gitAccess->setRole($manager->merge($this->getReference('role-'.$roleSlug)));
            $gitAccess->setReference($reference);
            $gitAccess->setIsRead($isRead);
            $gitAccess->setIsWrite($isWrite);
            $gitAccess->setIsAdmin($isAdmin);

            $manager->persist($gitAccess);
        }

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 300;
    }
}
