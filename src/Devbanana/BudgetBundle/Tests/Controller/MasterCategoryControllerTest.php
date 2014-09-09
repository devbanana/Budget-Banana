<?php

namespace Devbanana\BudgetBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MasterCategoryControllerTest extends WebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/show');
    }

}
