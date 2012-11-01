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

        $crawler = $this->client->request('GET', '/activate-email/'.$email->getActivationToken());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Email active', $crawler->filter('#splash-content h2')->text());

        $crawler = $this->client->request('GET', '/profile');

        $link = $crawler->filter('#email_'.$email->getId().' .email-default')->attr('href');
        $crawler = $this->client->request('POST', $link);

        $this->assertTrue($this->client->getResponse()->isRedirect('/profile'));
        $crawler = $this->client->followRedirect();
        $node    = $crawler->filter('.flash-messages p.success');

        $this->assertEquals(1, $node->count());
        $this->assertContains('Your default email address has been changed', $node->text());
    }

    public function testActiveEmailIncorrectHash()
    {
        $this->client->connect('alice');

        $em    = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $email = $em->getRepository('GitonomyCoreBundle:Email')->findOneByEmail('derpina@example.org');

        $crawler = $this->client->request('GET', '/email/'.$email->getUser()->getUsername().'/activate/azerty');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
