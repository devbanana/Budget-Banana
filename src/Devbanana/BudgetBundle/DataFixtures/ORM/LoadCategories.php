<?php

namespace Devbanana\BudgetBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Devbanana\BudgetBundle\Entity\MasterCategory;
use Devbanana\BudgetBundle\Entity\Category;

class LoadCategories implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $masterOrder = 0;
        $order = 0;

$giving = new MasterCategory;
$giving->setName('Giving');
$giving->setOrder($masterOrder++);
$manager->persist($giving);

$tithing = new Category;
$tithing->setName('Tithing');
$tithing->setOrder($order++);
$tithing->setMasterCategory($giving);
$manager->persist($tithing);

$charitable = new Category;
$charitable->setName('Charitable');
$charitable->setOrder($order++);
$charitable->setMasterCategory($giving);
$manager->persist($charitable);

$monthlyBills = new MasterCategory;
$monthlyBills->setOrder($masterOrder++);
$monthlyBills->setName('Monthly Bills');
$manager->persist($monthlyBills);

$rentMortgage = new Category;
$rentMortgage->setName('Rent/Mortgage');
$rentMortgage->setOrder($order++);
$rentMortgage->setMasterCategory($monthlyBills);
$manager->persist($rentMortgage);

$phone = new Category;
$phone->setName('Phone');
$phone->setOrder($order++);
$phone->setMasterCategory($monthlyBills);
$manager->persist($phone);

$internet = new Category;
$internet->setName('Internet');
$internet->setOrder($order++);
$internet->setMasterCategory($monthlyBills);
$manager->persist($internet);

$cableTv = new Category;
$cableTv->setName('Cable TV');
$cableTv->setOrder($order++);
$cableTv->setMasterCategory($monthlyBills);
$manager->persist($cableTv);

$electricity = new Category;
$electricity->setName('Electricity');
$electricity->setOrder($order++);
$electricity->setMasterCategory($monthlyBills);
$manager->persist($electricity);

$water = new Category;
$water->setName('Water');
$water->setMasterCategory($monthlyBills);
$water->setOrder($order++);
$manager->persist($water);

$gas = new Category;
$gas->setName('Natural Gas/Propane/Oil');
$gas->setOrder($order++);
$gas->setMasterCategory($monthlyBills);
$manager->persist($gas);

$everydayExpenses = new MasterCategory;
$everydayExpenses->setName('Everyday Expenses');
$everydayExpenses->setOrder($masterOrder++);
$manager->persist($everydayExpenses);

$groceries = new Category;
$groceries->setName('Groceries');
$groceries->setOrder($order++);
$groceries->setMasterCategory($everydayExpenses);
$manager->persist($groceries);

$fuel = new Category;
$fuel->setName('Fuel');
$fuel->setOrder($order++);
$fuel->setMasterCategory($everydayExpenses);
$manager->persist($fuel);

$money = new Category;
$money->setName('Spending Money');
$money->setOrder($order++);
$money->setMasterCategory($everydayExpenses);
$manager->persist($money);

$restaurants = new Category;
$restaurants->setName('Restaurants');
$restaurants->setOrder($order++);
$restaurants->setMasterCategory($everydayExpenses);
$manager->persist($restaurants);

$medical = new Category;
$medical->setName('Medical');
$medical->setOrder($order++);
$medical->setMasterCategory($everydayExpenses);
$manager->persist($medical);

$clothing = new Category;
$clothing->setName('Clothing');
$clothing->setMasterCategory($everydayExpenses);
$clothing->setOrder($order++);
$manager->persist($clothing);

$householdGoods = new Category;
$householdGoods->setName('Household Goods');
$householdGoods->setOrder($order++);
$householdGoods->setMasterCategory($everydayExpenses);
$manager->persist($householdGoods);

$rainyDayFunds = new MasterCategory;
$rainyDayFunds->setName('Rainy Day Funds');
$rainyDayFunds->setOrder($masterOrder++);
$manager->persist($rainyDayFunds);

$emergencyFund = new Category;
$emergencyFund->setName('Emergency Fund');
$emergencyFund->setOrder($order++);
$emergencyFund->setMasterCategory($rainyDayFunds);
$manager->persist($rainyDayFunds);

$carRepairs = new Category;
$carRepairs->setName('Car Repairs');
$carRepairs->setOrder($order++);
$carRepairs->setMasterCategory($rainyDayFunds);
$manager->persist($rainyDayFunds);

$homeMaintenance = new Category;
$homeMaintenance->setName('Home Maintenance');
$homeMaintenance->setOrder($order++);
$homeMaintenance->setMasterCategory($rainyDayFunds);
$manager->persist($homeMaintenance);

$carInsurance = new Category;
$carInsurance->setName('Car Insurance');
$carInsurance->setOrder($order++);
$carInsurance->setMasterCategory($rainyDayFunds);
$manager->persist($rainyDayFunds);

$lifeInsurance = new Category;
$lifeInsurance->setName('Life Insurance');
$lifeInsurance->setOrder($order++);
$lifeInsurance->setMasterCategory($rainyDayFunds);
$manager->persist($lifeInsurance);

$healthInsurance = new Category;
$healthInsurance->setName('Health Insurance');
$healthInsurance->setOrder($order++);
$healthInsurance->setMasterCategory($rainyDayFunds);
$manager->persist($healthInsurance);

$birthdays = new Category;
$birthdays->setName('Birthdays');
$birthdays->setOrder($order++);
$birthdays->setMasterCategory($rainyDayFunds);
$manager->persist($birthdays);

$christmas = new Category;
$christmas->setName('Christmas');
$christmas->setOrder($order++);
$christmas->setMasterCategory($rainyDayFunds);
$manager->persist($christmas);

$anniversary = new Category;
$anniversary->setName('Anniversary');
$anniversary->setOrder($order++);
$anniversary->setMasterCategory($rainyDayFunds);
$manager->persist($anniversary);

$savingsGoals = new MasterCategory;
$savingsGoals->setName('Savings Goals');
$savingsGoals->setOrder($masterOrder++);
$manager->persist($savingsGoals);

$carReplacement = new Category;
$carReplacement->setName('Car Replacement');
$carReplacement->setOrder($order++);
$carReplacement->setMasterCategory($savingsGoals);
$manager->persist($carReplacement);

$vacation = new Category;
$vacation->setName('Vacation');
$vacation->setOrder($order++);
$vacation->setMasterCategory($savingsGoals);
$manager->persist($vacation);

$debt = new MasterCategory;
$debt->setName('Debt');
$debt->setOrder($masterOrder++);
$manager->persist($debt);

$carPayment = new Category;
$carPayment->setName('Car Payment');
$carPayment->setMasterCategory($debt);
$carPayment->setOrder($order++);
$manager->persist($carPayment);

$studentLoanPayment = new Category;
$studentLoanPayment->setName('Student Loan Payment');
$studentLoanPayment->setOrder($order++);
$studentLoanPayment->setMasterCategory($debt);
$manager->persist($studentLoanPayment);

$personalLoanPayment = new Category;
$personalLoanPayment->setName('Personal Loan Payment');
$personalLoanPayment->setOrder($order++);
$personalLoanPayment->setMasterCategory($debt);
$manager->persist($personalLoanPayment);

$manager->flush();
    }
}

