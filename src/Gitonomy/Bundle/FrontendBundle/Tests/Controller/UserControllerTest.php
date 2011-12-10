<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegistrationIsOpened()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/en_US/register');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBasicRegister()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/en_US/register');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'user_registration[username]'         => 'test',
            'user_registration[fullname]'         => 'Test example',
            'user_registration[email]'            => 'test@example.org',
            'user_registration[password][first]'  => 'test',
            'user_registration[password][second]' => 'test',
        ));

        $crawler  = $client->submit($form);
        $response = $client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US/'));

        $crawler = $client->followRedirect();
        $node = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Your account was created!', $node->text());
    }
}
