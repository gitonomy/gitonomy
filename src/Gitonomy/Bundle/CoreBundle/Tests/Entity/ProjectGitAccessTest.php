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

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\ProjectGitAccess;

class ProjectGitAccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideMatches
     */
    public function testMatches($pattern, $reference, $expected)
    {
        $project = new Project('foo', 'foo');
        $access = new ProjectGitAccess($project);
        $access->setReference($pattern);

        $this->assertEquals($expected, $access->matches($reference));
    }

    public function provideMatches()
    {
        return array(
            array('*',       'refs/heads/master',   true),
            array('master',  'refs/heads/master',   true),
            array('feat-',   'refs/heads/feat-bug', false),
            array('feat-*',  'refs/heads/feat-bug', true)
        );
    }

    /**
     * @dataProvider provideVerifyPermission
     */
    public function testVerifyPermission($write, $admin, $permission, $expected)
    {
        $project = new Project('foo', 'foo');
        $access = new ProjectGitAccess($project);
        $access->setWrite($write);
        $access->setAdmin($admin);

        $this->assertEquals($expected, $access->verifyPermission($permission));
    }

    public function provideVerifyPermission()
    {
        return array(
            array(false, false, ProjectGitAccess::WRITE_PERMISSION, false),
            array(true,  false, ProjectGitAccess::WRITE_PERMISSION, true),
            array(true,  false, ProjectGitAccess::ADMIN_PERMISSION, false),
            array(true, true,   ProjectGitAccess::ADMIN_PERMISSION, true),
        );
    }
}
