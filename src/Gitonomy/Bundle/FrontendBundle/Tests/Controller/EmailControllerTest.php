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

class EmailControllerTest extends WebTestCase
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

    public function testActiveEmail()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');

        $crawler = $this->client->request('GET', '/en_US/email/'.$email->getUser()->getUsername().'/activate/'.$email->getActivationToken());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Email active', $crawler->filter('h1')->text());

        $crawler = $this->client->request('GET', '/en_US/profile/emails');

        $link = $crawler->filter('#email_'.$email->getId().' a:contains("as default")')->link();
        $crawler = $this->client->click($link);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/profile/emails'));
        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('div.alert-success');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Email "'.$email->getEmail().'" now as default.', $node->text());
    }

    public function testActiveEmailIncorrectHash()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');

        $crawler = $this->client->request('GET', '/en_US/email/'.$email->getUser()->getUsername().'/activate/azerty');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
