<?php

namespace Devbanana\BudgetBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BudgetControllerTest extends WebTestCase
{
    public function testBudget()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
    }

}
