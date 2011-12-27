<?php

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

    public function testCreateAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/create');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCreateAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/create');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Create new user', $crawler->filter('h1')->text());
    }

    public function testCreate()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/create');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user input[type=submit]')->form(array(
            'adminuser[username]'           => 'test',
            'adminuser[fullname]'           => 'test',
            'adminuser[timezone]'           => 'Europe/Paris',
            'adminuser[userRolesGlobal][1]' => false,
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/list'));
    }

    public function testCreateEmailExists()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/1/edit');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@example.org',
        ));

        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/1/edit'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-message.warning p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email you filled is not valid.', $node->text());
    }

    public function testCreateEmail()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/1/edit');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user_email input[type=submit]')->form(array(
            'useremail[email]' => 'admin@mydomain.tld',
        ));

        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/1/edit'));

        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email "admin@mydomain.tld" added.', $node->text());

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
        $crawler  = $this->client->request('GET', '/en_US/adminuser/1/edit');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Edit user "Admin"', $crawler->filter('h1')->text());
    }

    public function testEditBob()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/4/edit');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user input[type=submit]')->form(array(
            'adminuser[userRolesGlobal][1]' => true,
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/list'));
    }


    public function testEditRoleUser()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/1/edit');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#user_role_project input[type=submit]')->form(array(
            'adminuserroleproject[project]' => '2',
            'adminuserroleproject[role]'    => '4',
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/1/edit'));
    }

    public function testDeleteAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/adminuser/1/delete');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testDeleteAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/1/delete');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testDeleteAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/1/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Delete user "Admin" ?', $crawler->filter('h1')->text());
    }

    public function testDeleteBob()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/4/delete');
        $response = $this->client->getResponse();

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/list'));
    }

    public function testDeleteUserRole()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminuser/projectrole/3/delete');
        $response = $this->client->getResponse();

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminuser/4/edit'));
    }
}
