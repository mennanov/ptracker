<?php

namespace Ptracker\TasksBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    public function testIndex() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks', array(), array(), array('PHP_AUTH_USER' => 'testuser', 'PHP_AUTH_PW' => 'testing1234'));
        print $client->getResponse()->getContent();

        $this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
    }

}
