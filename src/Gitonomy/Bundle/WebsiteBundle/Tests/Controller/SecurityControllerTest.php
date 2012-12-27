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
        $crawler  = $this->client->request('GET', '/register');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form button[type=submit]')->form(array(
            'register[username]'         => 'test',
            'register[fullname]'         => 'Test example',
            'register[email]'            => 'test@example.org',
            'register[password][first]'  => 'test',
            'register[password][second]' => 'test',
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/login'));

        $crawler = $this->client->followRedirect();
        $node = $crawler->filter('div.alert-success');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Your account was created!', $node->text());
    }

    public function testLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->filter('form button[type=submit]')->form(array(
            '_username' => 'foo',
            '_password' => 'bar',
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('http://localhost/login'));

        $crawler = $this->client->followRedirect();

        $this->assertEquals('Bad credentials', $crawler->filter('.alert-error')->text());
    }

    public function testInactiveLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->filter('form button[type=submit]')->form(array(
            '_username' => 'inactive',
            '_password' => 'inactive',
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testRememberme()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->filter('form button[type=submit]')->form(array(
            '_username'    => 'alice',
            '_password'    => 'alice',
            '_remember_me' => true,
        ));

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertTrue($this->client->getResponse()->isRedirect('/'));
        $crawler = $this->client->followRedirect();

        $cookieJar = $this->client->getCookieJar();
        $this->assertNotNull($cookieJar->get('REMEMBERME'));

        $cookieJar->expire('MOCKSESSID');

        $crawler = $this->client->request('GET', '/');
        $this->assertEquals(1, $crawler->filter('#global-menu a:contains("Alice")')->count());

        $cookieJar->expire('MOCKSESSID');
        $cookieJar->expire('REMEMBERME');

        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(0, $crawler->filter('#splash-content a:contains("Alice")')->count());
    }

    public function testLogout()
    {
        $crawler = $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/');
        $link = $crawler->filter('#global-menu a:contains("Logout")');
        $this->assertEquals(1, $link->count());
        $this->client->click($link->link());

        $this->assertTrue($this->client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testForgotPassword()
    {
        $crawler = $this->client->request('GET', '/login');
        $link = $crawler->filter('#splash-content a:contains("Forgot your password?")');
        $this->assertEquals(1, $link->count());
        $crawler = $this->client->click($link->link());

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Forgot your password?', $crawler->filter('#splash-content h2')->text());

        $form = $crawler->filter('form button[type=submit]')->form(array(
            'email' => 'alice@example.org'
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/login'));

        $profile = $this->client->getProfile();
        $collector = $profile->getCollector('mailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testChangePassword()
    {
        $crawler  = $this->client->request('GET', '/forgot-password/alice/forgottokenalice');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('Change your password', $crawler->filter('#splash-content h2')->text());

        $form = $crawler->filter('form button[type=submit]')->form(array(
            'change_password[password][first]'  => 'foobar',
            'change_password[password][second]' => 'foobar'
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/login'));

        $crawler = $this->client->connect('alice', 'foobar');
        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('#global-menu a:contains("Alice")')->count());
    }

    public function testChangePasswordWithNoPassword()
    {
        $crawler  = $this->client->request('GET', '/forgot-password/alice/forgottokenalice');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('Change your password', $crawler->filter('#splash-content h2')->text());

        $form = $crawler->filter('form button[type=submit]')->form();

        $crawler  = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('#change_password:contains("This value should not be blank")')->count());
    }

    public function testChangePasswordWithExpiredToken()
    {
        $crawler  = $this->client->request('GET', '/forgot-password/bob/forgottokenbob');
        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testChangePasswordWithWrongToken()
    {
        $crawler  = $this->client->request('GET', '/forgot-password/alice/foobar');
        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }
}
