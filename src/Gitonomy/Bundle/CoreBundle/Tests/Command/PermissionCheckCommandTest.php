<?php

namespace Gitonomy\Bundle\CoreBundle\Tests\Command;

use Gitonomy\Bundle\CoreBundle\Test\CommandTestCase;

class PermissionCheckCommandTest extends CommandTestCase
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

    public function provideSimpleCase()
    {
        return array(
            array('alice', 'GIT_WRITE',      true,  'foobar'),
            array('alice', 'GIT_FORCE',      false, 'foobar'),
            array('alice', 'GIT_WRITE',      true,  'barbaz'),
            array('alice', 'GIT_FORCE',      true,  'barbaz'),
            array('admin', 'PROJECT_CREATE', true,  null),
        );
    }

    /**
     * @dataProvider provideSimpleCase
     */
    public function testSimpleCase($username, $permission, $expected, $projectSlug)
    {
        $projectSuffix = $projectSlug === null ? '' : '--project='.$projectSlug;

        $command = sprintf('gitonomy:permission-check %s %s %s', $projectSuffix, $username, $permission);
        list($statusCode , $output) = $this->runCommand($this->client, $command);

        $this->assertEquals($statusCode, $expected ? 0 : 1);
        $this->assertEquals('', $output); // Output must be empty, otherwise displayed to user pushing
    }
}
