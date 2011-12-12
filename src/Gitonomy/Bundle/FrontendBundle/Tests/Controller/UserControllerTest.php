<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @todo Find a way to test the project with mode "unregistered"
 * @todo Test logout
 */
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

    public function testRegistrationIsOpened()
    {
        $crawler  = $this->client->request('GET', '/en_US/register');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBasicRegister()
    {
        $crawler  = $this->client->request('GET', '/en_US/register');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'user_registration[username]'         => 'test',
            'user_registration[fullname]'         => 'Test example',
            'user_registration[email]'            => 'test@example.org',
            'user_registration[password][first]'  => 'test',
            'user_registration[password][second]' => 'test',
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US'));

        $crawler = $this->client->followRedirect();
        $node = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Your account was created!', $node->text());
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
}
