<?php

namespace Gitonomy\Bundle\CoreBundle\Tests\Command;

use Gitonomy\Bundle\CoreBundle\Test\CommandTestCase;

class GitCommandTest extends CommandTestCase
{
    protected $client;

    protected $shellHandler;

    public function setUp()
    {
        $this->client = self::createClient();

        $this->shellHandler = $this->getMockBuilder('Gitonomy\Bundle\CoreBundle\Git\ShellHandler')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->client->setShellHandler($this->shellHandler);

        $this->client->startIsolation();
    }

    public function tearDown()
    {
        $this->client->stopIsolation();
    }

    public function testShellInformations()
    {
        $this->shellHandler
            ->expects($this->once())
            ->method('getOriginalCommand')
            ->will($this->returnValue(null))
        ;

        $return = $this->runCommand($this->client, 'gitonomy:git --stderr=1 alice');

        $this->assertContains('You are identified as alice', $return);
        $this->assertRegexp('/Foobar[ ]+Developer[ ]/', $return);
        $this->assertRegexp('/Barbaz[ ]+Lead developer[ ]/', $return);
    }
}
