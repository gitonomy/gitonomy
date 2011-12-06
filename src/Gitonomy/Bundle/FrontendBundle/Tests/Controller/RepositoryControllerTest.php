<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RepositoryControllerTest extends WebTestCase
{
    public function testNotExisting()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/alice/not-existing');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testViewAsAnonymous()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/alice/foobar');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('alice/foobar', $crawler->filter('h1')->text());
        $this->assertEquals(0, $crawler->filter('a:contains(Fork)')->count());
    }

    public function testViewAliceBarbazAsBob()
    {
        $client = self::createClient();
        $client->connect('bob');

        $crawler  = $client->request('GET', '/alice/barbaz');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('alice/barbaz', $crawler->filter('h1')->text(), "Title is correct");
        $this->assertEquals(1, $crawler->filter('a:contains("Fork this repository")')->count(), "I see the fork button");
    }

    public function testViewAliceFoobarAsBob()
    {
        $client = self::createClient();
        $client->connect('bob');

        $crawler  = $client->request('GET', '/alice/foobar');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('alice/foobar', $crawler->filter('h1')->text(), "Title is correct");
        $this->assertEquals(1, $crawler->filter('a:contains("View my fork")')->count(), "I can access my fork");
    }
}
