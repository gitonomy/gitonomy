<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegistrationIsOpened()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/register');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBasicRegister()
    {
        $client = self::createClient();

        $crawler  = $client->request('GET', '/register');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'registration[username]'         => 'test',
            'registration[fullname]'         => 'Test example',
            'registration[email]'            => 'test@example.org',
            'registration[password][first]'  => 'test',
            'registration[password][second]' => 'test',
        ));

        $crawler  = $client->submit($form);
        $response = $client->getResponse();

        $this->assertTrue($response->isRedirect('/'));

        $crawler = $client->followRedirect();
        $node = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Your account was created!', $node->text());
    }
}

/**
 *             'registration' => array(
                'username'         => 'test',
            'fullname'         => 'Test example',
            'email'            => 'test@example.org',
            'password' => array(
                'first'  => 'test',
                'second' => 'test',

 */