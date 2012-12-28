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

class AdministrationRolesControllerTest extends WebTestCase
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

    public function testCreateAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/admin/roles/create');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testCreateAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/admin/roles/create');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCreate()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();

        $permissionA = $em->getRepository('GitonomyCoreBundle:Permission')->findOneByName('ROLE_ADMIN');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/admin/roles/create');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->client->submit($crawler->filter('form button[type=submit]')->form(), array(
            'administration_role[name]'        => 'test',
            'administration_role[slug]'        => 'slug',
            'administration_role[description]' => 'test',
            'administration_role[permissions]' => array(
                $permissionA->getId()
            ),
        ));

        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('test');

        $this->assertNotNull($role);
        $this->assertCount(1, $role->getPermissions());

        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/roles'));
    }

    public function testCreateNameExists()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/admin/roles/create');
        $response = $this->client->getResponse();

        $form = $crawler->filter('form button[type=submit]')->form(array(
            'administration_role[name]'        => 'Administrator',
            'administration_role[description]' => 'test'
        ));

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('#administration_role_name_field span:contains("This value is already used")')->count());
    }

    public function testEditAsAnonymous()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Administrator');

        $crawler  = $this->client->request('GET', '/admin/roles/'.$role->getId().'/edit');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testEditAsAlice()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Administrator');

        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/admin/roles/'.$role->getId().'/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testEdit()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Developer');

        $this->client->connect('admin');

        $crawler  = $this->client->request('GET', '/admin/roles/'.$role->getId().'/edit');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());

        $this->client->submit($crawler->filter('form button[type=submit]')->form(), array(
            'administration_role[name]'        => $role->getName(),
            'administration_role[description]' => $role->getDescription(),
        ));

        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/roles'));
        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testDelete()
    {
        $this->markTestSkipped();

        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Visitor');

        $this->client->connect('admin');


        $crawler  = $this->client->request('GET', '/admin/'.$role->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Delete role "Visitor" ?', $crawler->filter('h1')->text());

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/admin/roles'));
    }
}
