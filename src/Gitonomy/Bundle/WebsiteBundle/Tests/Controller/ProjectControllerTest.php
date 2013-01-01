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

    public function testNewsfeed()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertGreaterThan(1, $crawler->filter('td:contains("add an image")')->count());

        $crawler = $this->client->request('GET', '/projects/foobar?reference=pagination');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('view diff of 101 commits', $crawler->filter('small')->text());
    }

    public function testTree()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar/tree/master');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('td:contains("modify image")'));
    }

    public function testTreeHistory()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar/tree-history/master/image.jpg');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('td:contains("add an image")'));
    }

    public function testBranches()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar/branches');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('td:contains("Add element_100")'));
    }

    public function testTags()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar/tags');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCompare()
    {
        $this->client->connect('alice');

        $crawler = $this->client->request('GET', '/projects/foobar?reference=pagination');
        $crawler = $this->client->click($crawler->filter('a:contains("view diff of 101 commits")')->link());
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(101, $crawler->filter('table.commit-list tr')->count());
        $this->assertEquals(101, $crawler->filter('table.commit-summary tr')->count());
        $this->assertEquals(101, $crawler->filter('.changeset > .file')->count());
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

        $this->assertEquals(1, $crawler->filter('#project_name_field span:contains("This value is already used")')->count());
    }
}
