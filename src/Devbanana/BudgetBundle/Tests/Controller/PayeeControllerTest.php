<?php

namespace Devbanana\BudgetBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PayeeControllerTest extends WebTestCase
{
    public function testListajax()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/list/ajax');
    }

}
