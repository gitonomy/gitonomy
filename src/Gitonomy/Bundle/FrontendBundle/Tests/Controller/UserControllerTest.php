<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
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

    public function testShowAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/user/alice');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect());
    }

    public function testShowAsConnected()
    {
        $this->client->connect('bob');

        $crawler  = $this->client->request('GET', '/en_US/user/alice');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }
}
