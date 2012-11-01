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

class AdministrationUserControllerTest extends WebTestCase
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

    public function testListAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/admin/users');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testListAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/admin/users');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testListAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/admin/users');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Administration', $crawler->filter('h1')->text());
    }

    public function testCreateAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/admin/users/create');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testCreateAsConnected()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/admin/users/create');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCreate()
    {
        $this->client->connect('admin');

        $crawler  = $this->client->request('GET', '/admin/users/create');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form button[type=submit]')->form(array(
            'administration_user[username]' => 'test',
            'administration_user[fullname]' => 'test',
            'administration_user[timezone]' => 'Europe/Paris',
        ));

        $this->client->submit($form);

        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('test');

        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/users/'.$user->getId().'/edit'));

        $this->assertNotEmpty($user);
    }

    public function testEditAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/admin/users/1/edit');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testEditAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/admin/users/1/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testEditAsAdmin()
    {
        $this->client->connect('admin');

        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('bob');

        $crawler  = $this->client->request('GET', '/admin/users/'.$user->getId().'/edit');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form button[type=submit]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/users'));
    }

    public function testAdminCreateEmailExists()
    {
        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('admin');

        $this->client->connect('admin');

        $this->markTestSkipped();

        $crawler  = $this->client->request('GET', '/admin/'.$user->getId().'/emails');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@example.org',
        ));

        $crawler = $this->client->submit($form);
        $node    = $crawler->filter('#user_email span.help-inline');
        $this->assertEquals(1, $node->count());
        $this->assertEquals('This value is already used.', $node->text());
    }

    public function testAdminCreateEmail()
    {
        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('admin');

        $this->client->connect('admin');

        $this->markTestSkipped();

        $crawler  = $this->client->request('GET', '/admin/'.$user->getId().'/emails');
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

        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/'.$email->getUser()->getId().'/emails'));
        $crawler = $this->client->followRedirect();

        $node = $crawler->filter('div.alert-success');
        $this->assertEquals('Email "'.$email->getEmail().'" added.', $node->text());

        $node = $crawler->filter('#email_'.$email->getId().' td:contains("no")');
        $this->assertEquals(1, $node->count());
        $node = $crawler->filter('#email_'.$email->getId().' td:contains("yes")');
        $this->assertEquals(1, $node->count());
    }

    public function testDeleteAsAnonymous()
    {
        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('bob');

        $this->markTestSkipped();

        $crawler  = $this->client->request('GET', '/admin/'.$user->getId().'/delete');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testDeleteAsAlice()
    {
        $this->client->connect('alice');

        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('bob');

        $this->markTestSkipped();

        $crawler  = $this->client->request('GET', '/admin/'.$user->getId().'/delete');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testDeleteBob()
    {
        $this->client->connect('admin');

        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('bob');

        $this->markTestSkipped();

        $crawler  = $this->client->request('GET', '/admin/'.$user->getId().'/delete');
        $response = $this->client->getResponse();

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/users'));
    }
}
