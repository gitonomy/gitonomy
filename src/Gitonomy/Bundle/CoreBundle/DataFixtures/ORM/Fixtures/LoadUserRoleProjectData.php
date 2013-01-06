<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\Fixtures;

use Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;

/**
 * @author Julien DIDIER <julien@jdidier.net>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadUserRoleProjectData extends Fixture
{
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
        $empty  = $manager->merge($this->getReference('project-empty'));

        $userRoleProjects = array(
            // foobar
            new UserRoleProject($leadUser, $foobar, $lead),
            new UserRoleProject($alice,    $foobar, $dev),
            new UserRoleProject($bob,      $foobar, $dev),
            new UserRoleProject($charlie,  $foobar, $visitor),

            // barbaz
            new UserRoleProject($alice,    $barbaz, $lead),

            // empty
            new UserRoleProject($alice, $empty, $lead)
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
        return 4; // user, role, project
    }
}
