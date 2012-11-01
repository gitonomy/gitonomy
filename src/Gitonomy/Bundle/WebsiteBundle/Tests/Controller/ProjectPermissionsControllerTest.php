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

    public function testShowAsDisconnected()
    {
        $crawler  = $this->client->request('GET', '/projects/foobar');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testShowAsNotPermitted()
    {
        $this->client->connect('bob');

        $crawler  = $this->client->request('GET', '/projects/barbaz');
        $response = $this->client->getResponse();

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testShow()
    {
        $this->client->connect('alice');

        $crawler  = $this->client->request('GET', '/projects/foobar');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testShowEmpty()
    {
        $this->client->connect('alice');

        $crawler  = $this->client->request('GET', '/projects/empty');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testHistory()
    {
        $this->client->connect('alice');

        $crawler  = $this->client->request('GET', '/projects/foobar/history?reference=master');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testHistoryEmpty()
    {
        $this->client->connect('alice');

        $crawler  = $this->client->request('GET', '/projects/empty/history');
        $response = $this->client->getResponse();

        $this->assertEquals(500, $response->getStatusCode()); // fatal: bad default revision 'HEAD'
    }

    public function testHistoryViewOther()
    {
        $this->client->connect('alice');

        $crawler  = $this->client->request('GET', '/projects/foobar/history?reference=new-feature');
        $response = $this->client->getResponse();

        $this->assertCount(0, $crawler->filter('a:contains("Add a test script")'));

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testFeedBranch()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('a:contains("Add a test script")'));

        $crawler = $this->client->request('GET', '/projects/foobar?reference=pagination');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('small:contains("And 100 others...")'));
    }

    public function testTree()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar/tree/master');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('a:contains("Change the README filename")'));
    }

    public function testTreeHistory()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar/tree-history/master/README');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('a:contains("Change the README filename")'));
    }

    public function testTree_WithFile_DisplayContent()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar/tree/master/run.php');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('textarea:contains("Foo Bar")'));
    }

    public function testCreateProjectAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/create-project');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testCreateProjectAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/create-project');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCreateProject()
    {
        $this->repositoryPool
            ->expects($this->once())
            ->method('onProjectCreate')
        ;

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/create-project');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Create a new project', $crawler->filter('h1')->text());

        $form = $crawler->filter('form button[type=submit]')->form(array(
            'project[name]' => 'test',
            'project[slug]' => 'test',
        ));

        $this->client->submit($form);

        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('test');

        $this->assertNotEmpty($project);

        $this->assertTrue($this->client->getResponse()->isRedirect('/projects/'.$project->getSlug()));
    }

    public function testCreateProjectNameExists()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/create-project');
        $response = $this->client->getResponse();

        $form = $crawler->filter('form button[type=submit]')->form(array(
            'project[name]' => 'Foobar',
        ));

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('#project_name_field p:contains("This value is already used")')->count());
    }

    public function testPermissionsAsAnonymous()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $crawler  = $this->client->request('GET', '/projects/'.$project->getSlug().'/permissions');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testPermissionsAsAlice()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/projects/'.$project->getSlug().'/permissions');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testPermissions()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->client->connect('admin');

        $crawler  = $this->client->request('GET', '/projects/'.$project->getSlug().'/permissions');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('foobar', $crawler->filter('h1')->text());
    }

    public function testUserRoles()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
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
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');
        $user    = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('alice');
        $userRole = $em->getRepository('GitonomyCoreBundle:UserRoleProject')->findOneBy(array(
            'user'    => $user,
            'project' => $project
        ));

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/projects/'.$project->getSlug().'/permissions');
        $response = $this->client->getResponse();

        $this->markTestSkipped();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/adminproject/'.$project->getId().'/user-roles'));
    }

    public function testGitAccesses()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
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
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Developer');
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');
        $gitAccess = $em->getRepository('GitonomyCoreBundle:ProjectGitAccess')->findOneBy(array(
            'project'   => $project,
            'role'      => $role,
            'reference' => '*'
        ));
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->markTestSkipped();

        $this->assertTrue($this->client->getResponse()->isRedirect('/adminproject/'.$project->getId().'/git-accesses'));
    }

    public function testDeleteAsAnonymous()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->markTestSkipped();

        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testDeleteAsAlice()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->markTestSkipped();

        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testDeleteFoobar()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('barbaz');

        $this->repositoryPool
            ->expects($this->once())
            ->method('onProjectDelete')
        ;

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/adminproject/'.$project->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Delete project "Barbaz" ?', $crawler->filter('h1')->text());

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/adminproject/list'));

        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('barbaz');
        $this->assertNull($project);
    }
}
