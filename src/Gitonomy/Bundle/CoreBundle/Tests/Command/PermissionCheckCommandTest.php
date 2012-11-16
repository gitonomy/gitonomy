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
            array('foobar', 'alice', 'PROJECT_READ',       true  ),
            array('barbaz', 'bob',   'PROJECT_READ',       false ),
            array(null,     'admin', 'ROLE_ADMIN',         true ),
            array(null,     'alice', 'ROLE_ADMIN',         false ),
        );
    }

    /**
     * @dataProvider provideSimpleCase
     */
    public function testSimpleCase($projectSlug, $username, $permission, $expected)
    {
        $projectSuffix = $projectSlug === null ? '' : '--project='.$projectSlug;

        $command = sprintf('gitonomy:permission-check %s %s %s', $projectSuffix, $username, $permission);
        list($statusCode, $output) = $this->runCommand($this->client, $command);

        $this->assertEquals($statusCode, $expected ? 0 : 1);
        $this->assertEquals('', $output); // Output must be empty, otherwise displayed to user pushing
    }
}
