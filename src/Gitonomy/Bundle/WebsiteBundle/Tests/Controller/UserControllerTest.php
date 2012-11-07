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

class UserControllerTest extends WebTestCase
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

    public function testShowAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/user/alice');
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect());
    }

    public function provideShowAsConnected()
    {
        return array(
            array('bob',   'alice', array('Foobar')),
            array('alice', 'bob',   array('Foobar')),
            array('alice', 'alice', array('Foobar', 'Empty', 'Barbaz')),
            array('admin', 'alice', array('Foobar', 'Empty', 'Barbaz'))
        );
    }

    /**
     * @dataProvider provideShowAsConnected
     */
    public function testShowAsConnected($userFrom, $userTo, $expectedProjects)
    {
        $this->client->connect($userFrom);

        $crawler  = $this->client->request('GET', '/user/'.$userTo);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $projectTitles = $crawler->filter('#projects > tbody tr');
        $this->assertEquals(count($expectedProjects), $projectTitles->count());

        foreach ($expectedProjects as $i => $title) {
            $this->assertEquals($title, $projectTitles->eq($i)->filter('a')->text());
        }
    }
}
