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

class ProjectCreateCommandTest extends CommandTestCase
{
    protected $client;
    protected $repositoryPool;
    protected $hookInjector;

    public function setUp()
    {
        $this->client = self::createClient();
        $this->repositoryPool = $this->getMockBuilder('Gitonomy\Bundle\CoreBundle\Git\RepositoryPool')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->hookInjector = $this->getMockBuilder('Gitonomy\Bundle\CoreBundle\Git\HookInjector')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->client->setRepositoryPool($this->repositoryPool);
        $this->client->setHookInjector($this->hookInjector);

        $this->client->startIsolation();
    }

    public function tearDown()
    {
        $this->client->stopIsolation();
    }

    public function testSimpleCase()
    {
        $this->repositoryPool
            ->expects($this->once())
            ->method('onProjectCreate')
        ;
        $this->hookInjector
            ->expects($this->once())
            ->method('onProjectCreate')
        ;

        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:project-create "Sample name" sample-name');

        $this->assertEquals("Project Sample name was created!\n", $output);

        $em = $this->client->getKernel()->getContainer()->get('doctrine')->getManager();

        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBy(array(
            'name' => 'Sample name',
            'slug' => 'sample-name'
        ));

        $this->assertNotEmpty($project);
    }
}
