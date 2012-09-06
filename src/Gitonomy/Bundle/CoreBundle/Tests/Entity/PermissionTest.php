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

namespace Gitonomy\Bundle\CoreBundle\Tests\Entity;

use Gitonomy\Bundle\CoreBundle\Entity\Permission;

class PermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanciation()
    {
        $permission = new Permission('Name');

        $this->assertFalse($permission->isGlobal(), "Permission is not global by default");
        $this->assertEquals('Name', $permission->getName(), 'Name getter');

        $permission = new Permission('Name', true);

        $this->assertTrue($permission->isGlobal());
        $this->assertEquals('Name', $permission->getName(), 'Name getter');
    }
}
