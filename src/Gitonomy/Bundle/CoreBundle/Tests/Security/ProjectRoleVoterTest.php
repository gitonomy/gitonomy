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

namespace Gitonomy\Bundle\CoreBundle\Tests\Security;

use Gitonomy\Bundle\CoreBundle\Security\ProjectRoleVoter;
use Gitonomy\Bundle\CoreBundle\Security\ProjectRole;
use Gitonomy\Bundle\CoreBundle\Entity\Project;

class ProjectRoleVoterTest extends \PHPUnit_Framework_TestCase
{
    public function testNoRole()
    {
        $project = new Project('A', 'A');
        $token = $this->getToken(array(), $project);
        $voter = new ProjectRoleVoter();

        $this->assertEquals(ProjectRoleVoter::ACCESS_DENIED, $voter->vote($token, $project, array('PROJECT_FOO')));
    }

    public function testSimpleRole()
    {
        $project = new Project('A', 'A');
        $token = $this->getToken(array('PROJECT_FOO'), $project);
        $voter = new ProjectRoleVoter();

        $this->assertEquals(ProjectRoleVoter::ACCESS_GRANTED, $voter->vote($token, $project, array('PROJECT_FOO')));
    }

    public function testAndOKAttributes()
    {
        $project = new Project('A', 'A');
        $token = $this->getToken(array('PROJECT_FOO', 'PROJECT_BAR'), $project);
        $voter = new ProjectRoleVoter();

        $this->assertEquals(ProjectRoleVoter::ACCESS_GRANTED, $voter->vote($token, $project, array('PROJECT_FOO', 'PROJECT_BAR')));
    }

    public function testAndKOAttributes()
    {
        $project = new Project('A', 'A');
        $token = $this->getToken(array('PROJECT_FOO', 'PROJECT_BAR'), $project);
        $voter = new ProjectRoleVoter();

        $this->assertEquals(ProjectRoleVoter::ACCESS_DENIED, $voter->vote($token, $project, array('PROJECT_BAR', 'PROJECT_BAZ')));
    }

    public function testNotCorrectProject()
    {
        $project = new Project('A', 'A');
        $projectOther = new Project('B', 'B');
        $token = $this->getToken(array('PROJECT_FOO', 'PROJECT_BAR'), $project);
        $voter = new ProjectRoleVoter();

        $this->assertEquals(ProjectRoleVoter::ACCESS_DENIED, $voter->vote($token, $projectOther, array('PROJECT_FOO')), "Cannot access another project");
    }

    protected function getToken(array $roles, Project $project)
    {
        // replace with objects bound to project
        foreach ($roles as $i => $role) {
            $roles[$i] = new ProjectRole($project, $role);
        }

        $user = $this->getMock('Gitonomy\Bundle\CoreBundle\Entity\User');
        $user->expects($this->any())->method('getRoles')->will($this->returnValue($roles));

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())->method('getUser')->will($this->returnValue($user));

        return $token;
    }
}
