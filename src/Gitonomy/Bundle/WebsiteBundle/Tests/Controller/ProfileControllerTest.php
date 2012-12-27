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

namespace Gitonomy\Bundle\WebsiteBundle\Tests\Controller;

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
        $crawler  = $this->client->request('GET', '/profile');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testIndexAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/profile');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreateEmailExists()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/profile');

        $form = $crawler->filter('#user_email button[type=submit]')->form(array(
            'profile_email[email]' => 'admin@example.org',
        ));

        $crawler  = $this->client->submit($form);
        $node     = $crawler->filter('#user_email span.help-inline');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('This value is already used.', $node->text());
    }

    public function testCreateEmail()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/profile');

        $form = $crawler->filter('#user_email button[type=submit]')->form(array(
            'profile_email[email]' => 'admin@mydomain.tld',
        ));

        $crawler   = $this->client->submit($form);
        $response  = $this->client->getResponse();
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('mailer');

        $this->assertTrue($response->isRedirect('/profile'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('.flash-messages .success');

        $this->assertEquals(1, $node->count());
        $this->assertContains('New email was created', $node->text());
    }

    public function testEmailSendActivation()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');
        $this->assertNotEmpty($email);

        $crawler = $this->client->request('GET', '/profile');

        $link = $crawler->filter('#email_'.$email->getId().' .send-activation')->attr('href');
        $crawler = $this->client->request('POST', $link);

        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('mailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $this->assertTrue($this->client->getResponse()->isRedirect('/profile'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('.flash-messages .success');

        $this->assertEquals(1, $node->count());
        $this->assertContains('Activation mail sent', $node->text());
    }

    public function testDeleteEmail()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');

        $crawler = $this->client->request('GET', '/profile');

        $link = $crawler->filter('#email_'.$email->getId().' .email-delete')->attr('href');
        $crawler = $this->client->request('POST', $link);

        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/profile'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('.flash-messages p.success');

        $this->assertEquals(1, $node->count());
        $this->assertContains('Email deleted', $node->text());
    }

    public function testChangePasswordFail()
    {
        $this->client->connect('alice');

        $crawler  = $this->client->request('GET', '/profile/password');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form')->form(array(
            'profile_password[old_password]' => 'ecila',
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertFalse($response->isRedirect());
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(1, $crawler->filter('#profile_password_old_password_field.error')->count());
    }

    public function testChangePasswordOk()
    {
        $this->client->connect('alice');

        $crawler  = $this->client->request('GET', '/profile/password');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form')->form(array(
            'profile_password[old_password]' => 'alice',
            'profile_password[password][first]' => 'ecila',
            'profile_password[password][second]' => 'ecila',
        ));
        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/profile/password'));

        $this->client->logout();

        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->filter('form button[type=submit]')->form(array(
            '_username' => 'alice',
            '_password' => 'ecila'
        ));

        $crawler = $this->client->submit($form);
        $this->assertEquals('Projects', trim($crawler->filter('h1')->text()));
    }

    public function testSshKeyListAndCreate()
    {
        $this->client->connect('bob');

        $crawler  = $this->client->request('GET', '/profile/ssh-keys');
        $response = $this->client->getResponse();

        // List
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(2, $crawler->filter('.ssh-key')->count());
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("Installed key")')->count());
        $this->assertEquals(0, $crawler->filter('.ssh-key h3:contains("Installed key") + pre + p span.label-info')->count());
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("Not installed key")')->count());
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("Not installed key") span span.label-info')->count());

        // Create
        $form = $crawler->filter('form')->form(array(
            'profile_ssh_key[title]'   => 'foo',
            'profile_ssh_key[content]' => 'bar'
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/profile/ssh-keys'));

        $crawler  = $this->client->followRedirect();

        $this->assertEquals(3, $crawler->filter('.ssh-key')->count());

        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("foo")')->count());
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("foo") span span.label-info')->count());
    }

    public function testSshKeyCreateInvalid()
    {
        $this->client->connect('bob');

        $crawler  = $this->client->request('GET', '/profile/ssh-keys');
        $response = $this->client->getResponse();

        // Create
        $form = $crawler->filter('form')->form(array(
            'profile_ssh_key[title]'   => 'foo',
            'profile_ssh_key[content]' => 'bar'."\n"."baz"
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertFalse($response->isRedirect());

        $this->assertEquals("No newline permitted", $crawler->filter('#profile_ssh_key_content + div span.help-inline')->text());
    }

    public function testChangeWrongUsername()
    {
        $this->client->connect('bob');

        $this->markTestSkipped();

        $crawler  = $this->client->request('GET', '/profile/change-username');
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
        $this->markTestSkipped();

        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('inactive');

        $crawler = $this->client->request('GET', '/profile/'.$user->getUsername().'/activate/'.$user->getActivationToken());

        $form = $crawler->filter('#user input[type=submit]')->form(array(
            'change_password[password][first]'  => 'inactive',
            'change_password[password][second]' => 'inactive',
        ));

        $crawler  = $this->client->submit($form);

        $this->client->connect('inactive');
    }
}
