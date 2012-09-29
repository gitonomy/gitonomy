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
        list($statusCode, $output) = $this->execCommand('foobar', 'alice', '/refs/heads/master', 'd8930cd3f7e343195b85d56408a701feb98b95a8', '026537944abd3c0498b30e6d10916a64fe0988c4');

        $this->assertEquals(0, $statusCode);
        $this->assertEquals(null, $output);
    }

    public function testUndefinedProjectCase()
    {
        list($statusCode, $output) = $this->execCommand('example', 'alice', '/refs/heads/master', '0', '0');

        $this->assertEquals(1, $statusCode);
        $this->assertEquals(null, $output);
    }

    public function testNewReferenceCase()
    {
        list($statusCode, $output) = $this->execCommand('foobar', 'alice', '/refs/heads/example', 'd8930cd3f7e343195b85d56408a701feb98b95a8', '026537944abd3c0498b30e6d10916a64fe0988c4');

        $this->assertEquals(0, $statusCode);
        $this->assertEquals(null, $output);
    }

    public function testUndefinedUserCase()
    {
        list($statusCode, $output) = $this->execCommand('foobar', 'example', '/refs/heads/master', 'd8930cd3f7e343195b85d56408a701feb98b95a8', '026537944abd3c0498b30e6d10916a64fe0988c4');

        $this->assertEquals(1, $statusCode);
        $this->assertEquals(null, $output);
    }

    public function execCommand($project, $user, $reference, $before, $after)
    {
        return $this->runCommand($this->client, sprintf('gitonomy:project-notify-push %s %s %s %s %s',
            $project, $user, $reference, $before, $after
        ));
    }
}
