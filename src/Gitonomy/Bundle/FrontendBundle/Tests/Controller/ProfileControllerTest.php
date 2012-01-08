<?php

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
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/profile/emails'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-message.warning p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email you filled is not valid.', $node->text());
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
        $node    = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email "admin@mydomain.tld" added.', $node->text());
    }

    public function testSendEmailActivation()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');
        $this->assertNotEmpty($email);

        $crawler   = $this->client->request('GET', '/en_US/email/profile/'.$email->getId().'/send-activation');
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/profile/emails'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Activation mail for "'.$email->getEmail().'" sent.', $node->text());
    }

    public function testActiveEmail()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');

        $crawler = $this->client->request('GET', '/en_US/email/'.$email->getUser()->getUsername().'/activate/'.$email->getActivation());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Email active', $crawler->filter('h1')->text());

        $crawler = $this->client->request('GET', '/en_US/profile/emails');

        $link = $crawler->filter('#email_'.$email->getId().' a:contains("as default")')->link();
        $crawler = $this->client->click($link);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/profile/emails'));
        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email "'.$email->getEmail().'" now as default.', $node->text());
    }

    public function testAsDefaultUnactivedEmail()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');

        $crawler = $this->client->request('GET', '/en_US/email/profile/'.$email->getId().'/default');

        $this->assertEquals('500', $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteEmail()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');

        $crawler  = $this->client->request('GET', '/en_US/email/profile/'.$email->getId().'/delete');
        $form     = $crawler->filter('#delete input[type=submit]')->form();

        $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/profile/emails'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email "'.$email->getEmail().'" deleted.', $node->text());
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
        $this->assertEquals(0, $crawler->filter('.ssh-key h3:contains("Installed key") + pre + p span.notice')->count());
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("Not installed key")')->count());
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("Not installed key") + pre + p span.notice')->count());

        // Create
        $form = $crawler->filter('form')->form(array(
            'profile_ssh_key[title]'   => 'foo',
            'profile_ssh_key[content]' => 'bar'
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/profile/ssh-keys'));

        $crawler  = $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertEquals(3, $crawler->filter('.ssh-key')->count());

        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("foo")')->count());
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("foo") + pre + p span.notice')->count());
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
        $response = $this->client->getResponse();

        $this->assertEquals(1, $crawler->filter('.topbar a:contains("foobar")')->count());

        $crawler  = $this->client->request('GET', '/en_US/profile/ssh-keys');
        $response = $this->client->getResponse();

        // Check the installed key is marked as not installed
        $this->assertEquals(1, $crawler->filter('.ssh-key h3:contains("Installed key") + pre + p span.notice')->count());
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
        $response = $this->client->getResponse();

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
