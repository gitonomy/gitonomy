<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
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

    public function testLogin()
    {
        $crawler = $this->client->request('GET', '/en_US/login');

        $form = $crawler->filter('input[type=submit][value=Login]')->form(array(
            '_username' => 'foo',
            '_password' => 'bar'
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('http://localhost/en_US/login'));

        $crawler = $this->client->followRedirect();

        $this->assertEquals('Unable to login:', $crawler->filter('.alert-message strong')->text());
        $this->assertEquals(1, $crawler->filter('.topbar a:contains("Login")')->count());
    }

    public function testLogout()
    {
        $crawler = $this->client->connect('alice');

        $this->assertEquals(1, $crawler->filter('.topbar a:contains("alice")')->count());

        $this->client->click($crawler->filter('a:contains("Logout")')->link());

        $this->assertTrue($this->client->getResponse()->isRedirect('http://localhost/en_US'));
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('a:contains("Login")')->count());
    }
}
