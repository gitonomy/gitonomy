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
}
