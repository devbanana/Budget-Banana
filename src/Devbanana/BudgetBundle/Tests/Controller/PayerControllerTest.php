<?php

namespace Devbanana\BudgetBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PayerControllerTest extends WebTestCase
{
    public function testPayersgetlistajax()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/payers/list/ajax');
    }

}
