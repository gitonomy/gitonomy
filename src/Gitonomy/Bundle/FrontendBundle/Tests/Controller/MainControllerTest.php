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
        $this->assertEquals('gitonomy.sample', $crawler->filter('h1')->text());
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

    public function testNavigationMenuDisconnected()
    {
        $crawler = $this->client->request('GET', '/en_US');
        $this->assertEquals(0, $crawler->filter('.nav .projects')->count());
    }

    public function testNavigationMenuAsAlice()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/en_US');
        $this->assertEquals(1, $crawler->filter('.nav .projects')->count());
        $this->assertEquals(1, $crawler->filter('.nav .projects a:contains("Foobar")')->count());
        $this->assertEquals(1, $crawler->filter('.nav .projects a:contains("Barbaz")')->count());
        $this->assertEquals(0, $crawler->filter('.nav .projects a:contains("Create")')->count());
    }

    public function testNavigationMenuAsBob()
    {
        $this->client->connect('bob');

        $crawler = $this->client->request('GET', '/en_US');
        $this->assertEquals(1, $crawler->filter('.nav .projects')->count());
        $this->assertEquals(1, $crawler->filter('.nav .projects a:contains("Foobar")')->count());
        $this->assertEquals(0, $crawler->filter('.nav .projects a:contains("Barbaz")')->count());
        $this->assertEquals(0, $crawler->filter('.nav .projects a:contains("Create")')->count());
    }

    public function testNavigationMenuAsAdmin()
    {
        $this->client->connect('admin');

        $crawler = $this->client->request('GET', '/en_US');
        $this->assertEquals(1, $crawler->filter('.nav .projects')->count());
        $this->assertEquals(1, $crawler->filter('.nav .projects a:contains("Create")')->count());
    }
}
