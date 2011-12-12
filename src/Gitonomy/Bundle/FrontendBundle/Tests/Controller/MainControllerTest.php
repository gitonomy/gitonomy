<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
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

    public function testHomepageWithoutLocale()
    {
        $crawler  = $this->client->request('GET', '/');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US'));

    }

    public function testHomepage()
    {
        $crawler  = $this->client->request('GET', '/en_US');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Welcome to Gitonomy.sample!', $crawler->filter('h1')->text());
    }

    public function testSetLocaleWithoutReferer()
    {
        $crawler  = $this->client->request('GET', '/fr_FR/but-i-would-prefer-en_US');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US'));

    }

    public function testSetLocaleWithReferer()
    {
        $crawler  = $this->client->request('GET', '/fr_FR/but-i-would-prefer-en_US', array(), array(), array(
            'HTTP_REFERER' => '/fr_FR/foobar'
        ));
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/foobar'));

    }
}
