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

/**
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class ProjectPermissionsControllerTest extends WebTestCase
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

    public function testPermissionsAsAnonymous()
    {
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $crawler  = $this->client->request('GET', '/projects/'.$project->getSlug().'/permissions');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testPermissionsAsAlice()
    {
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/projects/'.$project->getSlug().'/permissions');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testPermissions()
    {
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->client->connect('admin');

        $crawler  = $this->client->request('GET', '/projects/'.$project->getSlug().'/permissions');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Foobar', $crawler->filter('h1')->text());
    }

    public function testUserRoles()
    {
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');
        $user    = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('admin');
        $role    = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Developer');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/projects/'.$project->getSlug().'/permissions');
        $response = $this->client->getResponse();

        $form = $crawler->filter('form#user-role input[type=submit]')->form(array(
            'project_role[user]' => $user->getId(),
            'project_role[role]' => $role->getId(),
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/projects/'.$project->getSlug().'/permissions'));

        $userRole = $em->getRepository('GitonomyCoreBundle:UserRoleProject')->findOneBy(array(
            'user'    => $user,
            'project' => $project
        ));
        $this->assertEquals($userRole->getRole()->getName(), $role->getName());
    }

    public function testUserRoleDelete()
    {
        $this->markTestSkipped();

        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');
        $user    = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('alice');
        $userRole = $em->getRepository('GitonomyCoreBundle:UserRoleProject')->findOneBy(array(
            'user'    => $user,
            'project' => $project
        ));

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/projects/'.$project->getSlug().'/permissions');
        $response = $this->client->getResponse();


        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/adminproject/'.$project->getId().'/user-roles'));
    }

    public function testGitAccesses()
    {
        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $role    = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Developer');
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/projects/'.$project->getSlug().'/permissions');
        $response = $this->client->getResponse();

        $form = $crawler->filter('form#git-access input[type=submit]')->form(array(
            'project_git_access[role]'      => $role->getId(),
            'project_git_access[reference]' => 'foobar',
            'project_git_access[write]'  => true,
            'project_git_access[admin]'  => true,
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/projects/'.$project->getSlug().'/permissions'));

        $gitAccess = $em->getRepository('GitonomyCoreBundle:ProjectGitAccess')->findOneBy(array(
            'role'      => $role,
            'reference' => 'foobar'
        ));

        $this->assertTrue($gitAccess->isWrite());
        $this->assertTrue($gitAccess->isAdmin());
    }

    public function testGitAccessDelete()
    {
        $this->markTestSkipped();

        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Developer');
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');
        $gitAccess = $em->getRepository('GitonomyCoreBundle:ProjectGitAccess')->findOneBy(array(
            'project'   => $project,
            'role'      => $role,
            'reference' => '*'
        ));
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');


        $this->assertTrue($this->client->getResponse()->isRedirect('/adminproject/'.$project->getId().'/git-accesses'));
    }
}
