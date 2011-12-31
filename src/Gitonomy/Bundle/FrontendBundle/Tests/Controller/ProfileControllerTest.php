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

    public function testProfileAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/profile');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testProfileAsAdmin()
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
        $crawler  = $this->client->request('GET', '/en_US/profile');

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@example.org',
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/profile'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-message.warning p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email you filled is not valid.', $node->text());
    }

    public function testCreateEmail()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/profile');

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@mydomain.tld',
        ));

        $crawler   = $this->client->submit($form);
        $response  = $this->client->getResponse();
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');

        $this->assertEquals(1, $collector->getMessageCount());

        $this->assertTrue($response->isRedirect('/en_US/profile'));

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

        $crawler   = $this->client->request('GET', '/en_US/email/profile/'.$email->getId().'/send-activation');
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/profile'));

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

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/profile'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email "'.$email->getEmail().'" actived.', $node->text());

        $link = $crawler->filter('#email_5 a:contains("as default")')->link();
        $crawler = $this->client->click($link);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/profile'));
        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email "'.$email->getEmail().'" now as default.', $node->text());
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

        $this->assertTrue($response->isRedirect('/en_US/profile'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email "'.$email->getEmail().'" deleted.', $node->text());
    }
}
