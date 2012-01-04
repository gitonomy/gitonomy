<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminRoleControllerTest extends WebTestCase
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
        $crawler  = $this->client->request('GET', '/en_US/adminrole/list');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testListAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/list');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testListAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/list');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Role management', $crawler->filter('h1')->text());
        $this->assertEquals(4, $crawler->filter('table thead tr th')->count());
    }

    public function testCreateAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/adminrole/create');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testCreateAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/create');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCreate()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();

        $permissionA = $em->getRepository('GitonomyCoreBundle:Permission')->findOneByName('GIT_READ');
        $permissionB = $em->getRepository('GitonomyCoreBundle:Permission')->findOneByName('GIT_WRITE');
        $permissionC = $em->getRepository('GitonomyCoreBundle:Permission')->findOneByName('GIT_FORCE');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/create');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Create new role', $crawler->filter('h1')->text());

        $form = $crawler->filter('#role input[type=submit]')->form(array(
            'adminrole[name]'        => 'test',
            'adminrole[description]' => 'test',
            'adminrole[permissions]' => array(
                $permissionA->getId(),
                $permissionB->getId(),
                $permissionC->getId()
            ),
        ));

        $this->client->submit($form);

        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('test');

        $this->assertCount(3, $role->getPermissions());

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminrole/'.$role->getId().'/edit'));
    }

    public function testCreateNameExists()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/create');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#role input[type=submit]')->form(array(
            'adminrole[name]'        => 'Administrator',
            'adminrole[description]' => 'test'
        ));

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('#adminrole_name_field p:contains("This value is already used")')->count());
    }

    public function testEditAsAnonymous()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Administrator');

        $crawler  = $this->client->request('GET', '/en_US/adminrole/'.$role->getId().'/edit');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testEditAsAlice()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Administrator');

        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/'.$role->getId().'/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testEdit()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Administrator');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/'.$role->getId().'/edit');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Edit role "Administrator"', $crawler->filter('h1')->text());

        $form = $crawler->filter('#role input[type=submit]')->form(array(
            'adminrole[permissions]' => array(),
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminrole/'.$role->getId().'/edit'));
        $this->client->followRedirect();
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode()); // @todo Explain me, I don't understand
    }

    public function testDeleteAsAnonymous()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Administrator');

        $crawler  = $this->client->request('GET', '/en_US/adminrole/'.$role->getId().'/delete');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testDeleteAsAlice()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Administrator');

        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/'.$role->getId().'/delete');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testDelete()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Project manager');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/'.$role->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Delete role "Project manager" ?', $crawler->filter('h1')->text());

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminrole/list'));
    }
}
