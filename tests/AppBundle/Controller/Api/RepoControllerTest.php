<?php

namespace Tests\AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RepoControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            '/api/show',
            array(
                'repoNameOne' => 'http://github.com/symfony/symfony',
                'repoNameTwo' => 'django/django'
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('Content-Type'));
    }

    public function testIndexFail()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            '/api/show',
            array(
                'repoNameOne' => 'symfonysymfony',
                'repoNameTwo' => 'djangodjango'
            )
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertEquals('application/problem+json', $client->getResponse()->headers->get('Content-Type'));
    }
}