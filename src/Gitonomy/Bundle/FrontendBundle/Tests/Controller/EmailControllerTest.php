<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmailControllerTest extends WebTestCase
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

    public function testAdminCreateEmailExists()
    {
        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('admin');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/edit');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@example.org',
        ));

        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/'.$user->getId().'/edit'));
        $crawler = $this->client->followRedirect();

        $node = $crawler->filter('div.alert-message.warning p');
        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email you filled is not valid.', $node->text());
    }

    public function testAdminCreateEmail()
    {
        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('admin');

        $this->client->connect('admin');

        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/edit');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@mydomain.tld',
        ));

        $crawler = $this->client->submit($form);

        // no mail sent
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(0, $collector->getMessageCount());

        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('admin@mydomain.tld');

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/'.$email->getUser()->getId().'/edit'));
        $crawler = $this->client->followRedirect();

        $node = $crawler->filter('div.alert-message.success p');
        $this->assertEquals('Email "'.$email->__toString().'" added.', $node->text());

        $node = $crawler->filter('#email_'.$email->getId().' td:contains("no")');
        $this->assertEquals(1, $node->count());
        $node = $crawler->filter('#email_'.$email->getId().' td:contains("yes")');
        $this->assertEquals(1, $node->count());
    }

    public function testProfileCreateEmailExists()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/profile/emails');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@example.org',
        ));

        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/profile/emails'));
        $crawler = $this->client->followRedirect();

        $node = $crawler->filter('div.alert-message.warning p');
        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email you filled is not valid.', $node->text());
    }

    public function testProfileCreateEmail()
    {
        $this->client->connect('admin');

        $crawler  = $this->client->request('GET', '/en_US/profile/emails');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@mydomain.tld',
        ));

        $crawler = $this->client->submit($form);

        // activation mail sent
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('admin@mydomain.tld');

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/profile/emails'));
        $crawler = $this->client->followRedirect();

        $node = $crawler->filter('div.alert-message.success p');
        $this->assertEquals('Email "'.$email->__toString().'" added.', $node->text());

        $node = $crawler->filter('#email_'.$email->getId().' td:contains("no")');
        $this->assertEquals(2, $node->count());
        $node = $crawler->filter('#email_'.$email->getId().' td a:contains("send activation")');
        $this->assertEquals(1, $node->count());
    }

    public function testProfileSendActivation()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');

        $crawler  = $this->client->request('GET', '/en_US/email/profile/'.$email->getId().'/send-activation');
        $response = $this->client->getResponse();

        // activation mail sent
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/profile/emails'));
        $crawler = $this->client->followRedirect();

        $node = $crawler->filter('div.alert-message.success p');
        $this->assertEquals('Activation mail for "'.$email->__toString().'" sent.', $node->text());

        $node = $crawler->filter('#email_'.$email->getId().' td a:contains("send activation")');
        $this->assertEquals(1, $node->count());
    }
}
