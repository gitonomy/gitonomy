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

class AdminProjectControllerTest extends WebTestCase
{
    protected $client;
    protected $repositoryPool;
    protected $hookInjector;

    public function setUp()
    {
        $this->client = self::createClient();
        $this->repositoryPool = $this->getMockBuilder('Gitonomy\Bundle\CoreBundle\Git\RepositoryPool')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->hookInjector = $this->getMockBuilder('Gitonomy\Bundle\CoreBundle\Git\HookInjector')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->client->setRepositoryPool($this->repositoryPool);
        $this->client->setHookInjector($this->hookInjector);

        $this->client->startIsolation();
    }

    public function tearDown()
    {
        $this->client->stopIsolation();
    }

    public function testListAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/adminproject/list');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testListAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/list');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testListAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/list');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Project management', $crawler->filter('h1')->text());
        $this->assertEquals(2, $crawler->filter('table thead tr th')->count());
    }

    public function testCreateAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/adminproject/create');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testCreateAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/create');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCreate()
    {
        $this->repositoryPool
            ->expects($this->once())
            ->method('onProjectCreate')
        ;

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/create');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Create new project', $crawler->filter('h1')->text());

        $form = $crawler->filter('#project input[type=submit]')->form(array(
            'adminproject[name]' => 'test',
            'adminproject[slug]' => 'test',
        ));

        $this->client->submit($form);

        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('test');

        $this->assertNotEmpty($project);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminproject/'.$project->getId().'/edit'));
    }

    public function testCreateNameExists()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/create');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#project input[type=submit]')->form(array(
            'adminproject[name]'        => 'Foobar',
        ));

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('#adminproject_name_field p:contains("This value is already used")')->count());
    }

    public function testEditAsAnonymous()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $crawler  = $this->client->request('GET', '/en_US/adminproject/'.$project->getId().'/edit');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testEditAsAlice()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/1/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testEdit()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->client->connect('admin');

        $crawler  = $this->client->request('GET', '/en_US/adminproject/1/edit');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Edit project "Foobar"', $crawler->filter('h1')->text());
        $this->assertEquals(0, $crawler->filter("#adminproject_slug")->count(), 'no slug field when project edition');

        $form = $crawler->filter('#project input[type=submit]')->form(array(
            'adminproject[name]' => 'test',
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminproject/1/edit'));

        $em->refresh($project);
        $this->assertEquals('test', $project->getName());
    }

    public function testUserRoles()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');
        $user    = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername('admin');
        $role    = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Developer');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/'.$project->getId().'/user-roles');
        $response = $this->client->getResponse();

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'adminuserroleproject[user]' => $user->getId(),
            'adminuserroleproject[role]' => $role->getId(),
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminproject/'.$project->getId().'/user-roles'));

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
        $crawler  = $this->client->request('GET', '/en_US/adminproject/user-roles/'.$userRole->getId().'/delete');
        $response = $this->client->getResponse();

        $form = $crawler->filter('input[type=submit]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminproject/'.$project->getId().'/user-roles'));
    }

    public function testGitAccesses()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $role    = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName('Developer');
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/'.$project->getId().'/git-accesses');
        $response = $this->client->getResponse();

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'project_git_access[role]'      => $role->getId(),
            'project_git_access[reference]' => 'foobar',
            'project_git_access[read]'   => true,
            'project_git_access[write]'  => true,
            'project_git_access[admin]'  => true,
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminproject/'.$project->getId().'/git-accesses'));

        $gitAccess = $em->getRepository('GitonomyCoreBundle:ProjectGitAccess')->findOneBy(array(
            'role'      => $role,
            'reference' => 'foobar'
        ));

        $this->assertTrue($gitAccess->isRead());
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

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/git-accesses/'.$gitAccess->getId().'/delete');
        $response = $this->client->getResponse();

        $form = $crawler->filter('input[type=submit]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminproject/'.$project->getId().'/git-accesses'));
    }

    public function testDeleteAsAnonymous()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $crawler  = $this->client->request('GET', '/en_US/adminproject/'.$project->getId().'/delete');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testDeleteAsAlice()
    {
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('foobar');

        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/'.$project->getId().'/delete');
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
        $crawler  = $this->client->request('GET', '/en_US/adminproject/'.$project->getId().'/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Delete project "Barbaz" ?', $crawler->filter('h1')->text());

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminproject/list'));

        $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug('barbaz');
        $this->assertNull($project);
    }
}
