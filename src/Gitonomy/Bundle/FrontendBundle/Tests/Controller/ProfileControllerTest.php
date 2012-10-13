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

class ProfileControllerTest extends WebTestCase
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

    public function testIndexAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/profile');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testIndexAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/profile');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertRegexp('/Profile/', $crawler->filter('h1')->text());
    }

    public function testCreateEmailExists()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/profile/emails');

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@example.org',
        ));

        $crawler  = $this->client->submit($form);
        $node     = $crawler->filter('#user_email span.help-inline');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('This value is already used.', $node->text());
    }

    public function testCreateEmail()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/profile/emails');

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@mydomain.tld',
        ));

        $crawler   = $this->client->submit($form);
        $response  = $this->client->getResponse();
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');

        $this->assertEquals(1, $collector->getMessageCount());

        $this->assertTrue($response->isRedirect('/en_US/profile/emails'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-success');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email "admin@mydomain.tld" added.', $node->text());
    }

    public function testEmailSendActivation()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');
        $this->assertNotEmpty($email);

        $crawler   = $this->client->request('GET', '/en_US/profile/emails/'.$email->getId().'/send-activation');
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/profile/emails'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-success');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Activation mail for "'.$email->getEmail().'" sent.', $node->text());
    }

    public function testAsDefaultUnactivedEmail()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');

        $crawler = $this->client->request('GET', '/en_US/profile/emails/'.$email->getId().'/default');

        $this->assertEquals('500', $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteEmail()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');

        $crawler  = $this->client->request('GET', '/en_US/profile/emails/'.$email->getId().'/delete');
        $form     = $crawler->filter('#delete input[type=submit]')->form();

        $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/profile/emails'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-success');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email "'.$email->getEmail().'" deleted.', $node->text());
    }

    public function testChangePasswordFail()
    {
        $this->client->connect('alice');

        $crawler  = $this->client->request('GET', '/en_US/profile/password');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form')->form(array(
            'profile_password[oldPassword]' => 'ecila',
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertFalse($response->isRedirect());
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(1, $crawler->filter('#profile_password_oldPassword_field.error')->count());
    }

    public function testChangePasswordOk()
    {
        $this->client->connect('alice');

        $crawler  = $this->client->request('GET', '/en_US/profile/password');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form')->form(array(
            'profile_password[oldPassword]' => 'alice',
            'profile_password[password][first]' => 'ecila',
            'profile_password[password][second]' => 'ecila',
        ));
        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/profile/password'));

        $this->client->logout();

        $crawler = $this->client->request('GET', '/en_US/login');
        $form = $crawler->filter('form input[type=submit][value="Login"]')->form(array(
            '_username' => 'alice',
            '_password' => 'ecila'
        ));
        $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('http://localhost/en_US'));
    }

    public function testSshKeyListAndCreate()
    {
        $this->client->connect('bob');

        $crawler  = $this->client->request('GET', '/en_US/profile/ssh-keys');
        $response = $this->client->getResponse();

        // List
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(2, $crawler->filter('.ssh-key')->count());

        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("Installed key")')->count());
        $this->assertEquals(0, $crawler->filter('.ssh-key h3:contains("Installed key") + pre + p span.label-info')->count());
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("Not installed key")')->count());
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("Not installed key") + pre + p span.label-info')->count());

        // Create
        $form = $crawler->filter('form')->form(array(
            'profile_ssh_key[title]'   => 'foo',
            'profile_ssh_key[content]' => 'bar'
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/profile/ssh-keys'));

        $crawler  = $this->client->followRedirect();

        $this->assertEquals(3, $crawler->filter('.ssh-key')->count());

        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("foo")')->count());
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("foo") + pre + p span.label-info')->count());
    }

    public function testSshKeyCreateInvalid()
    {
        $this->client->connect('bob');

        $crawler  = $this->client->request('GET', '/en_US/profile/ssh-keys');
        $response = $this->client->getResponse();

        // Create
        $form = $crawler->filter('form')->form(array(
            'profile_ssh_key[title]'   => 'foo',
            'profile_ssh_key[content]' => 'bar'."\n"."baz"
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertFalse($response->isRedirect());

        $this->assertEquals("No newline permitted", $crawler->filter('#profile_ssh_key_content + p span.help-inline')->text());
    }

    public function testChangeUsername()
    {
        $this->client->connect('bob');

        $crawler  = $this->client->request('GET', '/en_US/profile/change-username');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Change username', $crawler->filter('h1')->text());

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'change_username[username]' => 'foobar'
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/profile/change-username'));

        $crawler  = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('.navbar a:contains("foobar")')->count());
    }

    public function testChangeWrongUsername()
    {
        $this->client->connect('bob');

        $crawler  = $this->client->request('GET', '/en_US/profile/change-username');
        $response = $this->client->getResponse();

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'change_username[username]' => 'foo bar'
        ));

        $crawler  = $this->client->submit($form);

        $this->assertFalse($response->isRedirect());

        $this->assertEquals(1, $crawler->filter('#change_username_username_field.error')->count());
    }

    public function testActivate()
    {
        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('inactive');

        $crawler = $this->client->request('GET', '/en_US/profile/'.$user->getUsername().'/activate/'.$user->getActivationToken());

        $form = $crawler->filter('#user input[type=submit]')->form(array(
            'change_password[password][first]'  => 'inactive',
            'change_password[password][second]' => 'inactive',
        ));

        $crawler  = $this->client->submit($form);

        $this->client->connect('inactive');
    }
}
