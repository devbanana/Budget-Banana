<?php

namespace Devbanana\BudgetBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Devbanana\BudgetBundle\Entity\AccountCategory;

class LoadAccountCategories implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
$budgetAsset = array(
        'Checking',
        'Savings',
        'Cash',
        'Paypal',
        'Merchant Account',
        );

$budgetLiability = array(
        'Credit Card',
        'Line of Credit',
        );

$offBudgetAsset = array(
        'Investment Account',
        'Other Assets',
        );

$offBudgetLiability = array(
        'Mortgage',
        'Other Loan/Liabilities',
        );

$order = 0;

foreach ($budgetAsset as $category)
{
$ac = new AccountCategory;
$ac->setName($category);
$ac->setType('asset');
$ac->setBudgeted(true);
$ac->setOrder($order++);
$manager->persist($ac);
}

foreach ($budgetLiability as $category)
{
$ac = new AccountCategory;
$ac->setName($category);
$ac->setType('liability');
$ac->setBudgeted(true);
$ac->setOrder($order++);
$manager->persist($ac);
}

foreach ($offBudgetAsset as $category)
{
$ac = new AccountCategory;
$ac->setName($category);
$ac->setType('asset');
$ac->setBudgeted(false);
$ac->setOrder($order++);
$manager->persist($ac);
}

foreach ($offBudgetLiability as $category)
{
$ac = new AccountCategory;
$ac->setName($category);
$ac->setType('liability');
$ac->setBudgeted(false);
$ac->setOrder($order++);
$manager->persist($ac);
}

$manager->flush();
    }
}

