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

class ConfigShowCommandTest extends CommandTestCase
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

    public function testNoParameter()
    {
        list($statusCode ,$output) = $this->runCommand($this->client, "gitonomy:config:show");

        $this->assertContains('repository_path  '.$this->getRepositoryPath(), $output);
        $this->assertRegexp('/\n$/', $output, 'Ends with an empty line');
    }

    public function testCorrectParameter()
    {
        list($statusCode ,$output) = $this->runCommand($this->client, "gitonomy:config:show repository_path");

        $this->assertEquals($this->getRepositoryPath()."\n", $output);
    }

    public function testIncorrectParameter()
    {
        list($statusCode ,$output) = $this->runCommand($this->client, "gitonomy:config:show foobarbaz");

        $this->assertNotEquals(0, $statusCode);
    }

    private function getRepositoryPath()
    {
        return self::createClient()->getKernel()->getContainer()->getParameter('repository_path');
    }
}
