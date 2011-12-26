<?php

namespace Gitonomy\Bundle\CoreBundle\Tests\Command;

use Gitonomy\Bundle\CoreBundle\Test\CommandTestCase;

class UserCreateCommandTest extends CommandTestCase
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
        $output = $this->runCommand($this->client, 'gitonomy:user-create foo bar "foo@example.org" "Foo"');

        $this->assertEquals("The user foo was successfully created!\n", $output);

        $em = $this->client->getKernel()->getContainer()->get('doctrine')->getEntityManager();

        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneBy(array(
            'username' => 'foo'
        ));

        $this->assertInstanceOf('Gitonomy\Bundle\CoreBundle\Entity\User', $user);

        $this->assertEquals('foo@example.org', $user->getDefaultEmail());
        $this->assertEquals('Foo', $user->getFullname());
    }
}
