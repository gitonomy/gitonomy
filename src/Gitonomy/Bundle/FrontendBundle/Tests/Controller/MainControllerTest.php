<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testHomepageWithoutLocale()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/'));

    }

    public function testHomepage()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/en_US/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Welcome to Gitonomy.sample!', $crawler->filter('h1')->text());
    }

    public function testSetLocaleWithoutReferer()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/fr_FR/but-i-would-prefer-en_US');
        $response = $client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/'));

    }

    public function testSetLocaleWithReferer()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/fr_FR/but-i-would-prefer-en_US', array(), array(), array(
            'HTTP_REFERER' => '/fr_FR/foobar'
        ));
        $response = $client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/foobar'));

    }
}
