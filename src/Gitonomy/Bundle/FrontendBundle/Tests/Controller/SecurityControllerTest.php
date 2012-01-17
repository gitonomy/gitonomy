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

    public function testInactiveLogin()
    {
        $crawler = $this->client->request('GET', '/en_US/login');

        $form = $crawler->filter('input[type=submit][value=Login]')->form(array(
            '_username' => 'inactive',
            '_password' => 'inactive',
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('http://localhost/en_US/login'));
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

    public function testForgotPassword()
    {
        $crawler  = $this->client->request('GET', '/en_US/password');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Forgot your password ?', $crawler->filter('h1')->text());

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'forgot_password_request[email]' => 'alice@example.org'
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/password'));

        $profile = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $messages = $collector->getMessages();
        $message = $messages[0];

        $to = $message->getTo();
        $this->assertTrue(isset($to['alice@example.org']));
        $this->assertEquals('Alice', $to['alice@example.org']);
        $this->assertContains('Retrieve your password', $message->getSubject());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('.alert-message.success')->count());
    }

    public function testChangePassword()
    {
        $crawler  = $this->client->request('GET', '/en_US/password/alice/forgottokenalice');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('Change password for Alice', $crawler->filter('h1')->text());

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'change_password[password][first]'  => 'foobar',
            'change_password[password][second]' => 'foobar'
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US'));

        $crawler = $this->client->connect('alice', 'foobar');

        $this->assertEquals(1, $crawler->filter('.topbar a:contains("alice")')->count());
    }

    public function testChangePasswordWithNoPassword()
    {
        $crawler  = $this->client->request('GET', '/en_US/password/alice/forgottokenalice');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('Change password for Alice', $crawler->filter('h1')->text());

        $form = $crawler->filter('form input[type=submit]')->form();

        $crawler  = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('#change_password p:contains("This value should not be blank")')->count());
    }

    public function testChangePasswordWithExpiredToken()
    {
        $crawler  = $this->client->request('GET', '/en_US/password/bob/forgottokenbob');
        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testChangePasswordWithWrongToken()
    {
        $crawler  = $this->client->request('GET', '/en_US/password/alice/foobar');
        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }
}
