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

namespace Gitonomy\Bundle\DistributionBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VerificationControllerTest extends WebTestCase
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

    public function testIndex()
    {
        $crawler  = $this->client->request('GET', '/_configurator/verification');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), "Page responses correctly");
        $this->assertEquals('Verifications', $crawler->filter('h1')->text(), "Title is present");
    }

    public function testCheckMail()
    {
        $crawler  = $this->client->request('POST', '/_configurator/verification');
        $form     = $crawler->filter('form.check-mail')->form();

        $this->client->submit($form, array(
            'verification_mail[email]' => 'foo@example.org'
        ));

        $profile   = $this->client->getProfile();
        $collector = $profile->getCollector('swiftmailer');
        $this->assertEquals(1, $collector->getMessageCount());
    }
}
