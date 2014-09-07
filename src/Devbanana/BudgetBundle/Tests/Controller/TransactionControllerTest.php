<?php

namespace Devbanana\BudgetBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransactionControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/transactions');
    }

    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/transactions/{id}');
    }

    public function testNew()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/transactions/new');
    }

    public function testEdit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/transactions/{id}/edit');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/transactions/{id}/delete');
    }

}
