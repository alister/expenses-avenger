<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Expenses")')->count() > 0);
        // the resason this is zero is that it's made by the base.html.twig template.
        $this->assertTrue($crawler->filter('html:contains("Alister")')->count() == 0);
        // The post-angular page *does* have 'Expenses, by Alister Bulman' (in small, bold, etc)
    }
}
