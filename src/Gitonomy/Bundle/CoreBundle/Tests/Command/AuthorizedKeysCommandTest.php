<?php

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
        $output = $this->runCommand($this->client, "gitonomy:authorized-keys");

        $this->assertContains('app/console gitonomy:git alice" alice-key',           $output);
        $this->assertContains('app/console gitonomy:git bob" bob-key-installed',     $output);
        $this->assertContains('app/console gitonomy:git bob" bob-key-not-installed', $output);
        $this->assertRegexp('/\n$/', $output, 'Ends with an empty line');

        $lines = explode("\n", $output);
        $this->assertEquals(4, count($lines));

        $em = $this->client->getKernel()->getContainer()->get('doctrine')->getEntityManager();

        $notInstalled = $em->getRepository('GitonomyCoreBundle:UserSshKey')->findOneBy(array(
            'content' => 'bob-key-not-installed'
        ));

        $this->assertFalse($notInstalled->getIsInstalled());
    }

    public function testOptionInstall()
    {
        $output = $this->runCommand($this->client, "gitonomy:authorized-keys -i");

        $em = $this->client->getKernel()->getContainer()->get('doctrine')->getEntityManager();

        $notInstalled = $em->getRepository('GitonomyCoreBundle:UserSshKey')->findOneBy(array(
            'content' => 'bob-key-not-installed'
        ));

        $this->assertTrue($notInstalled->getIsInstalled());
    }
}
