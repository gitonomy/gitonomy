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

namespace Gitonomy\Bundle\CoreBundle\Tests\Command;

use Gitonomy\Bundle\CoreBundle\Test\CommandTestCase;

class GitAccessCommandTest extends CommandTestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = self::createClient();
        $this->client->startIsolation();
    }

    public function tearDown()
    {
        $this->client->stopIsolation();
    }

    public function testSimpleCreateCase()
    {
        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:git-access create barbaz admin master 1 1');

        $this->assertEquals(0, $statusCode, 'statusCode is equal to 0');
        $this->assertEquals("The git-access was successfully created!\n", $output);

        $doctrine = $this->client->getKernel()->getContainer()->get('doctrine');

        $project   = $doctrine->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('barbaz');
        $role      = $doctrine->getRepository('GitonomyCoreBundle:Role')->findOneBySlug('admin');
        $gitAccess = $doctrine->getRepository('GitonomyCoreBundle:ProjectGitAccess')->findOneBy(array(
            'project'   => $project,
            'role'      => $role,
            'reference' => 'master',
        ));

        $this->assertNotNull($gitAccess);
    }

    public function testSimpleDeleteCase()
    {
        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:git-access delete foobar visitor *');

        $this->assertEquals(0, $statusCode, 'statusCode is equal to 0');
        $this->assertEquals("The git-access was successfully deleted!\n", $output);

        $doctrine = $this->client->getKernel()->getContainer()->get('doctrine');

        $project   = $doctrine->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');
        $role      = $doctrine->getRepository('GitonomyCoreBundle:Role')->findOneBySlug('visitor');
        $gitAccess = $doctrine->getRepository('GitonomyCoreBundle:ProjectGitAccess')->findOneBy(array(
            'project'   => $project,
            'role'      => $role,
            'reference' => 'master',
        ));

        $this->assertNull($gitAccess);
    }
}
