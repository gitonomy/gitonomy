<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @todo Find a way to test the project with mode "unregistered"
 */
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

    public function testRegistrationIsOpened()
    {
        $crawler  = $this->client->request('GET', '/en_US/register');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBasicRegister()
    {
        $crawler  = $this->client->request('GET', '/en_US/register');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $form = $crawler->filter('form input[type=submit]')->form(array(
            'user_registration[username]'         => 'test',
            'user_registration[fullname]'         => 'Test example',
            'user_registration[defaultEmail]'     => 'test@example.org',
            'user_registration[password][first]'  => 'test',
            'user_registration[password][second]' => 'test',
        ));

        $crawler  = $this->client->submit($form);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isRedirect('/en_US'));

        $crawler = $this->client->followRedirect();
        $node = $crawler->filter('div.alert-message.success p');

        $this->assertEquals(1, $node->count());
        $this->assertEquals('Your account was created!', $node->text());
    }
}
