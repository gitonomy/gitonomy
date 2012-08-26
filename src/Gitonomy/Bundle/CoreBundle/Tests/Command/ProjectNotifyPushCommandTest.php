<?php

namespace Gitonomy\Bundle\CoreBundle\Tests\Command;

use Gitonomy\Bundle\CoreBundle\Test\CommandTestCase;

class ProjectNotifyPushCommandTest extends CommandTestCase
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

    public function testSimpleCase()
    {
        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:project-notify-push foobar alice 0 0 0');

        $this->assertEquals(0, $statusCode);
        $this->assertEquals(null, $output);
    }

    public function testUndefinedProjectCase()
    {
        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:project-notify-push example alice 0 0 0');

        $this->assertEquals(1, $statusCode);
        $this->assertEquals(null, $output);
    }

    public function testUndefinedUserCase()
    {
        list($statusCode ,$output) = $this->runCommand($this->client, 'gitonomy:project-notify-push foobar example 0 0 0');

        $this->assertEquals(1, $statusCode);
        $this->assertEquals(null, $output);
    }
}
