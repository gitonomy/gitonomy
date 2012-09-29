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

        $this->assertEquals(200, $response->getStatusCode(), "Page responses correctly");
        $this->assertEquals('Role management', $crawler->filter('h1')->text(), "Title is present");
        $this->assertEquals(2, $crawler->filter('table thead tr th')->count(), "Table has 2 columns");
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

        $permissionA = $em->getRepository('GitonomyCoreBundle:Permission')->findOneByName('ROLE_ADMIN');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/create');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Create new role', $crawler->filter('h1')->text());

        $this->client->submit($crawler->filter('#role input[type=submit]')->form(), array(
            'adminrole[name]'        => 'test',
            'adminrole[slug]'        => 'slug',
            'adminrole[description]' => 'test',
            'adminrole[permissions]' => array(
                $permissionA->getId()
            ),
        ));

        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('test');

        $this->assertNotNull($role);
        $this->assertCount(1, $role->getPermissions());

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
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Developer');

        $this->client->connect('admin');

        $crawler  = $this->client->request('GET', '/en_US/adminrole/'.$role->getId().'/edit');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('Edit role "Developer"', $crawler->filter('h1')->text());

        $this->client->submit($crawler->filter('#role input[type=submit]')->form(), array(
            'adminrole[name]'        => $role->getName(),
            'adminrole[description]' => $role->getDescription(),
        ));

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminrole/'.$role->getId().'/edit'));
        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
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
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Visitor');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminrole/'.$role->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Delete role "Visitor" ?', $crawler->filter('h1')->text());

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminrole/list'));
    }
}
