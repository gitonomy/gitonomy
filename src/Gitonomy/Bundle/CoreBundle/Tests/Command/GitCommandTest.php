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

        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:git --stderr=no alice');

        $this->assertContains('You are identified as alice', $output);
        $this->assertRegexp('/Foobar[ ]+Developer[ ]/', $output);
        $this->assertRegexp('/Barbaz[ ]+Lead developer[ ]/', $output);
    }

    public function testIllegalAction()
    {
        $this->shellHandler
            ->expects($this->once())
            ->method('getOriginalCommand')
            ->will($this->returnValue('git-bla-pack \'foobar.git\''))
        ;

        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:git --stderr=no alice');

        $this->assertContains('Action seems illegal', $output);
    }

    public function testPullNonAuthorizedProject()
    {
        $this->shellHandler
            ->expects($this->once())
            ->method('getOriginalCommand')
            ->will($this->returnValue('git-upload-pack \'barbaz.git\''))
        ;

        $this->shellHandler
            ->expects($this->never())
            ->method('handle')
        ;

        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:git --stderr=no bob');

        $this->assertContains('You are not allowed', $output);
    }

    public function testPullAuthorizedProject()
    {
        $this->shellHandler
            ->expects($this->once())
            ->method('getOriginalCommand')
            ->will($this->returnValue('git-upload-pack \'foobar.git\''))
        ;

        $this->shellHandler
            ->expects($this->once())
            ->method('handle')
        ;

        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:git --stderr=no bob');
    }

    public function testPushNonAuthorizedProject()
    {
        $this->shellHandler
            ->expects($this->once())
            ->method('getOriginalCommand')
            ->will($this->returnValue('git-receive-pack \'barbaz.git\''))
        ;

        $this->shellHandler
            ->expects($this->never())
            ->method('handle')
        ;

        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:git --stderr=no bob');

        $this->assertContains('You are not allowed', $output);
    }

    public function testPushAuthorizedProject()
    {
        $this->shellHandler
            ->expects($this->once())
            ->method('getOriginalCommand')
            ->will($this->returnValue('git-receive-pack \'foobar.git\''))
        ;

        $this->shellHandler
            ->expects($this->once())
            ->method('handle')
        ;

        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:git --stderr=no bob');
    }
}
