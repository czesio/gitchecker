<?php

namespace Tests\AppBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RepoControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/web');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $this->assertTrue(
            $client->getResponse()->isRedirect('/web/new'),
            'response is a redirect to /web/new'
        );
    }

    public function testNewComparision()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/web/new');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Get comparision', $crawler->filter('.form-comparision button')->text());

    }

    public function testNewComparisionSuccess()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/web/new');

        $form = $crawler->selectButton('gihub_package_comparision_form[save]')->form();
        $form['gihub_package_comparision_form[firstRepoName]'] = 'symfony/symfony';
        $form['gihub_package_comparision_form[secondRepoName]'] = 'django/django';
        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('h4')->count());
        $this->assertContains('symfony/symfony', $crawler->filter('p.first-record')->text());
        $this->assertContains('django/django', $crawler->filter('p.second-record')->text());

    }


    public function testNewComparisionFail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/web/new');

        $form = $crawler->selectButton('gihub_package_comparision_form[save]')->form();
        $form['gihub_package_comparision_form[firstRepoName]'] = 'symfonysymfony';
        $form['gihub_package_comparision_form[secondRepoName]'] = 'djangodjango';
        $crawler = $client->submit($form);
        $this->assertEmpty($crawler->filter('h4')->count());
        $this->assertContains('mhm, please check if the repos names are correct', $crawler->filter('p.repos-info')->text());

    }
}
