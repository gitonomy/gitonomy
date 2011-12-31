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

    public function testRegister()
    {
        $crawler  = $this->client->request('GET', '/en_US/register');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'user_registration[username]'            => 'test',
            'user_registration[fullname]'            => 'Test example',
            'user_registration[defaultEmail][email]' => 'test@example.org',
            'user_registration[password][first]'     => 'test',
            'user_registration[password][second]'    => 'test',
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
            '_password' => 'bar',
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('http://localhost/en_US/login'));

        $crawler = $this->client->followRedirect();

        $this->assertEquals('Unable to login:', $crawler->filter('.alert-message strong')->text());
        $this->assertEquals(1, $crawler->filter('.topbar a:contains("Login")')->count());
    }

    public function testRememberme()
    {
        $crawler = $this->client->request('GET', '/en_US/login');

        $form = $crawler->filter('input[type=submit][value=Login]')->form(array(
            '_username'    => 'alice',
            '_password'    => 'alice',
            '_remember_me' => true,
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('http://localhost/en_US'));
        $crawler = $this->client->followRedirect();

        $cookieJar = $this->client->getCookieJar();
        $this->assertNotNull($cookieJar->get('REMEMBERME'));

        $cookieJar->expire('PHPSESSID');

        $crawler = $this->client->request('GET', '/en_US');
        $this->assertEquals(1, $crawler->filter('.topbar a:contains("alice")')->count());

        $cookieJar->expire('PHPSESSID');
        $cookieJar->expire('REMEMBERME');

        $crawler = $this->client->request('GET', '/en_US');
        $this->assertEquals(0, $crawler->filter('.topbar a:contains("alice")')->count());
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
