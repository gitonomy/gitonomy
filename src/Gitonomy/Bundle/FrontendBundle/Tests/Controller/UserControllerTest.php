<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testOpenedRegistration()
    {
        return;
        $_SERVER['SYMFONY__GITONOMY__OPEN_REGISTRATION'] = true;
        $client = self::createClient();

        $crawler  = $client->request('GET', '/register');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testClosedRegistration()
    {
        $_SERVER['SYMFONY__GITONOMY__OPEN_REGISTRATION'] = false;
        $client = self::createClient();

        $crawler  = $client->request('GET', '/register');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }
}
