<?php

namespace Gitonomy\Bundle\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminProjectControllerTest extends WebTestCase
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

    public function testListAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/adminproject/list');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testListAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/list');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testListAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/list');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Project management', $crawler->filter('h1')->text());
        $this->assertEquals(2, $crawler->filter('table thead tr th')->count());
    }

    public function testCreateAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/adminproject/create');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testCreateAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/create');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCreateAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/create');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Create new project', $crawler->filter('h1')->text());
    }

    public function testCreate()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/create');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#project input[type=submit]')->form(array(
            'adminproject[name]' => 'test',
            'adminproject[slug]' => 'test',
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminproject/list'));
    }

    public function testCreateNameExists()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/create');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#project input[type=submit]')->form(array(
            'adminproject[name]'        => 'Foobar',
        ));

        $crawler = $this->client->submit($form);

        $this->assertEquals(1, $crawler->filter('#adminproject_name_field p:contains("This value is already used")')->count());
    }

    public function testEditAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/adminproject/1/edit');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testEditAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/1/edit');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testEditAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/1/edit');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Edit project "Foobar"', $crawler->filter('h1')->text());
    }

    public function testEditFoobar()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/1/edit');
        $response = $this->client->getResponse();

        $form = $crawler->filter('#project input[type=submit]')->form(array(
            'adminproject[name]' => 'test',
            'adminproject[slug]' => 'test',
        ));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminproject/list'));
    }

    public function testDeleteAsAnonymous()
    {
        $crawler  = $this->client->request('GET', '/en_US/adminproject/1/delete');
        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirect('http://localhost/en_US/login'));
    }

    public function testDeleteAsAlice()
    {
        $this->client->connect('alice');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/1/delete');
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testDeleteAsAdmin()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/2/delete');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Delete project "Barbaz" ?', $crawler->filter('h1')->text());
    }

    public function testDeleteFoobar()
    {
        $this->client->connect('admin');
        $crawler  = $this->client->request('GET', '/en_US/adminproject/2/delete');
        $response = $this->client->getResponse();

        $form = $crawler->filter('input[type=submit][value=Delete]')->form();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/en_US/adminproject/list'));
    }
}
