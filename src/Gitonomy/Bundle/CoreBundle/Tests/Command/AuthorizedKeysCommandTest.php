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

class AuthorizedKeysCommandTest extends CommandTestCase
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

    public function testDefaultDoesNotInstall()
    {
        list($statusCode ,$output) = $this->runCommand($this->client, "gitonomy:authorized-keys");

        $this->assertContains('app/console gitonomy:git alice" alice-key',           $output);
        $this->assertContains('app/console gitonomy:git bob" bob-key-installed',     $output);
        $this->assertContains('app/console gitonomy:git bob" bob-key-not-installed', $output);
        $this->assertRegexp('/\n$/', $output, 'Ends with an empty line');

        $lines = explode("\n", $output);
        $this->assertEquals(4, count($lines));

        $em = $this->client->getKernel()->getContainer()->get('doctrine')->getManager();

        $notInstalled = $em->getRepository('GitonomyCoreBundle:UserSshKey')->findOneBy(array(
            'content' => 'bob-key-not-installed'
        ));

        $this->assertNotNull($notInstalled);
        $this->assertFalse($notInstalled->isInstalled());
    }

    public function testOptionInstall()
    {
        list($statusCode ,$output) = $this->runCommand($this->client, "gitonomy:authorized-keys -i");

        $em = $this->client->getKernel()->getContainer()->get('doctrine')->getManager();

        $notInstalled = $em->getRepository('GitonomyCoreBundle:UserSshKey')->findOneBy(array(
            'content' => 'bob-key-not-installed'
        ));

        $this->assertTrue($notInstalled->isInstalled());
    }
}
