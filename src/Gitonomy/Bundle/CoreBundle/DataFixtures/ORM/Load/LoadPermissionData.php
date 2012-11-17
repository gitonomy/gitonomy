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

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\Load;

use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\Permission;
use Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\Fixture;

/**
 * @author Julien DIDIER <julien@jdidier.net>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadPermissionData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $permissions = array(
            new Permission('ROLE_ADMIN', true),
            new Permission('ROLE_PROJECT_CREATE', true),
            new Permission('PROJECT_READ', false),
            new Permission('PROJECT_ADMIN', false)
        );

        foreach ($permissions as $permission) {
            $manager->persist($permission);
            $this->setReference('permission-'.$permission->getName(), $permission);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
