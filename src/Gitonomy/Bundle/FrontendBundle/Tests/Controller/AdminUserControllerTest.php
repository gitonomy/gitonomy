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

class AdminUserControllerTest extends WebTestCase
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
        $crawler  = $this->client->request('GET', '/en_US/adminuser/list');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testListAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/list');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testListAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/list');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Users', $crawler->filter('h1')->text());
        $this->assertEquals(4, $crawler->filter('table thead tr th')->count());
    }

    public function testCreateAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/adminuser/create');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testCreateAsConnected()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/create');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCreate()
    {
        $this->client->connect('admin');

        $crawler  = $this->client->request('GET', '/en_US/adminuser/create');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Create new user', $crawler->filter('h1')->text());

        $form = $crawler->filter('#user input[type=submit]')->form(array(
            'adminuser[username]'           => 'test',
            'adminuser[fullname]'           => 'test',
            'adminuser[timezone]'           => 'Europe/Paris',
        ));

        $this->client->submit($form);

        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('test');

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/'.$user->getId().'/edit'));

        $this->assertNotEmpty($user);
    }

    public function testEditAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/adminuser/1/edit');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testEditAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/1/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testEditAsAdmin()
    {
        $this->client->connect('admin');

        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('bob');

        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/edit');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Edit user "Bob"', $crawler->filter('h1')->text());

        $form = $crawler->filter('#user input[type=submit]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/'.$user->getId().'/edit'));
    }

    public function testSendActivationForAdmin()
    {
        $this->client->connect('admin');

        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('admin');

        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/activate');
        $response = $this->client->getResponse();

        // no mail sent
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(0, $collector->getMessageCount());

        $this->assertEquals(500, $response->getStatusCode());

    }

    public function testSendActivation()
    {
        $this->client->connect('admin');

        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('inactive');

        $this->client->followRedirects(false);
        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/activate');
        $response = $this->client->getResponse();
        $this->assertNotEquals(500, $response->getStatusCode());
        $this->client->followRedirects(true);

        // no mail sent
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/'.$user->getId().'/edit'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-success');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Activation mail for user "'.$user->getFullname().'" sent.', $node->text());
    }

    public function testEditRoleUser()
    {
        $this->client->connect('admin');

        $em      = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user    = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('admin');
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneByName('Barbaz');
        $role    = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Developer');

        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/userrole');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user_role_project input[type=submit]')->form(array(
            'adminuserroleproject[project]' => $project->getId(),
            'adminuserroleproject[role]'    => $role->getId(),
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/'.$user->getId().'/userrole'));
    }

    public function testAdminCreateEmailExists()
    {
        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('admin');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/emails');
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

        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/emails');
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

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/'.$email->getUser()->getId().'/emails'));
        $crawler = $this->client->followRedirect();

        $node = $crawler->filter('div.alert-success');
        $this->assertEquals('Email "'.$email->getEmail().'" added.', $node->text());

        $node = $crawler->filter('#email_'.$email->getId().' td:contains("no")');
        $this->assertEquals(1, $node->count());
        $node = $crawler->filter('#email_'.$email->getId().' td:contains("yes")');
        $this->assertEquals(1, $node->count());
    }

    public function testEmailSendActivation()
    {
        $this->client->connect('admin');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');
        $this->assertNotEmpty($email);
        $user  = $email->getUser();

        $crawler   = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/emails/'.$email->getId().'/send-activation');
        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/'.$user->getId().'/emails'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-success');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Activation mail for email "'.$email->getEmail().'" sent.', $node->text());
    }

    public function testDeleteAsAnonymous()
    {
        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('bob');

        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/delete');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testDeleteAsAlice()
    {
        $this->client->connect('alice');

        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('bob');

        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/delete');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testDeleteBob()
    {
        $this->client->connect('admin');

        $em   = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('bob');

        $crawler  = $this->client->request('GET', '/en_US/adminuser/'.$user->getId().'/delete');
        $response = $this->client->getResponse();

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/list'));
    }

    public function testDeleteUserRole()
    {
        $this->client->connect('admin');
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();

        $user        = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('alice');
        $project     = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');
        $projectRole = $em->getRepository('GitonomyCoreBundle:UserRoleProject')->findOneBy(array(
            'user'    => $user,
            'project' => $project
        ));

        $crawler  = $this->client->request('GET', '/en_US/adminuser/projectrole/'.$projectRole->getId().'/delete');
        $response = $this->client->getResponse();

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/'.$user->getId().'/userrole'));
    }
}
