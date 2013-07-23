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
class ProjectControllerTest extends WebTestCase
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

    public function testCompare()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar?branch=pagination');
        $crawler = $this->client->click($crawler->filter('a:contains("view diff of 101 commits")')->link());
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(101, $crawler->filter('table.git-log td.message')->count());
        $this->assertEquals(101, $crawler->filter('.file-wrapper > .file')->count());
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
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreateProjectAsUser()
    {
        $this->client->connect('user');
        $crawler  = $this->client->request('GET', '/create-project');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/login'));
    }

    public function testCreateProject()
    {
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

        $this->repositoryPool
            ->expects($this->once())
            ->method('onProjectCreate')
        ;

        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/create-project');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('New project', $crawler->filter('h1')->text());

        $form = $crawler->filter('form button[type=submit]')->form(array(
            'project[name]' => 'test',
            'project[slug]' => 'test',
        ));

        $this->client->submit($form);

        $em = $this->client->getContainer()->get('doctrine')->getManager();
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

        $this->assertEquals(1, $crawler->filter('#project_name_field span:contains("This value is already used")')->count());
    }
}
