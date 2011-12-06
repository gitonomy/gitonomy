<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testHomepage()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Welcome to Gitonomy.sample!', $crawler->filter('h1')->text());
    }

    public function testDashboard()
    {
        $client = self::createClient();

        $client->connect('alice');

        $crawler  = $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('My repositories', $crawler->filter('h2')->eq(1)->text());

        $repositoryGitonomy = $crawler->filter('.well')->eq(0);
        $this->assertEquals('alice/foobar', $repositoryGitonomy->filter('h3 a')->text());
        $this->assertEquals('The Foo Bar project', $repositoryGitonomy->filter('p')->text());
    }
}
