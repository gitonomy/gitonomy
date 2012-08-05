<?php

namespace Gitonomy\Bundle\CoreBundle\Tests\Security;

use Gitonomy\Bundle\CoreBundle\Security\ProjectRole;
use Gitonomy\Bundle\CoreBundle\Entity\Project;

class ProjectRoleTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanciation()
    {
        $project = new Project('project', 'project');
        $role = new ProjectRole($project, 'FOO');
        $this->assertTrue($role->isProject($project));
        $this->assertNull($role->getRole());
        $this->assertEquals('FOO', $role->getProjectRole());
    }
}
