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

    public function testDashboard()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Your repositories', $crawler->filter('h1')->text());

        $this->assertEquals(3, $crawler->filter('h2')->count());

        $expectations = array(
            'Foobar' => '3 branches',
            'Barbaz' => '1 branch',
            'Empty'  => ''
        );

        foreach ($expectations as $project => $small) {
            $text = $crawler->filter('a:contains("'.$project.'") + small')->text();
            $text = trim(preg_replace("/[ \t\n\r]+/", " ", $text));
            $this->assertEquals($small, $text);
        }
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
