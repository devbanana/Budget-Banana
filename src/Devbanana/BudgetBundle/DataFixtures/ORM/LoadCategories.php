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
$giving = new MasterCategory;
$giving->setName('Giving');
$manager->persist($giving);

$tithing = new Category;
$tithing->setName('Tithing');
$tithing->setMasterCategory($giving);
$manager->persist($tithing);

$charitable = new Category;
$charitable->setName('Charitable');
$charitable->setMasterCategory($giving);
$manager->persist($charitable);

$monthlyBills = new MasterCategory;
$monthlyBills->setName('Monthly Bills');
$manager->persist($monthlyBills);

$rentMortgage = new Category;
$rentMortgage->setName('Rent/Mortgage');
$rentMortgage->setMasterCategory($monthlyBills);
$manager->persist($rentMortgage);

$phone = new Category;
$phone->setName('Phone');
$phone->setMasterCategory($monthlyBills);
$manager->persist($phone);

$internet = new Category;
$internet->setName('Internet');
$internet->setMasterCategory($monthlyBills);
$manager->persist($internet);

$cableTv = new Category;
$cableTv->setName('Cable TV');
$cableTv->setMasterCategory($monthlyBills);
$manager->persist($cableTv);

$electricity = new Category;
$electricity->setName('Electricity');
$electricity->setMasterCategory($monthlyBills);
$manager->persist($electricity);

$water = new Category;
$water->setName('Water');
$water->setMasterCategory($monthlyBills);
$manager->persist($water);

$gas = new Category;
$gas->setName('Natural Gas/Propane/Oil');
$gas->setMasterCategory($monthlyBills);
$manager->persist($gas);


$everydayExpenses = new MasterCategory;
$everydayExpenses->setName('Everyday Expenses');
$manager->persist($everydayExpenses);

$groceries = new Category;
$groceries->setName('Groceries');
$groceries->setMasterCategory($everydayExpenses);
$manager->persist($groceries);

$fuel = new Category;
$fuel->setName('Fuel');
$fuel->setMasterCategory($everydayExpenses);
$manager->persist($fuel);

$money = new Category;
$money->setName('Spending Money');
$money->setMasterCategory($everydayExpenses);
$manager->persist($money);

$restaurants = new Category;
$restaurants->setName('Restaurants');
$restaurants->setMasterCategory($everydayExpenses);
$manager->persist($restaurants);

$medical = new Category;
$medical->setName('Medical');
$medical->setMasterCategory($everydayExpenses);
$manager->persist($medical);

$clothing = new Category;
$clothing->setName('Clothing');
$clothing->setMasterCategory($everydayExpenses);
$manager->persist($clothing);

$householdGoods = new Category;
$householdGoods->setName('Household Goods');
$householdGoods->setMasterCategory($everydayExpenses);
$manager->persist($householdGoods);

$rainyDayFunds = new MasterCategory;
$rainyDayFunds->setName('Rainy Day Funds');
$manager->persist($rainyDayFunds);

$emergencyFund = new Category;
$emergencyFund->setName('Emergency Fund');
$emergencyFund->setMasterCategory($rainyDayFunds);
$manager->persist($rainyDayFunds);

$carRepairs = new Category;
$carRepairs->setName('Car Repairs');
$carRepairs->setMasterCategory($rainyDayFunds);
$manager->persist($rainyDayFunds);

$homeMaintenance = new Category;
$homeMaintenance->setName('Home Maintenance');
$homeMaintenance->setMasterCategory($rainyDayFunds);
$manager->persist($homeMaintenance);

$carInsurance = new Category;
$carInsurance->setName('Car Insurance');
$carInsurance->setMasterCategory($rainyDayFunds);
$manager->persist($rainyDayFunds);

$lifeInsurance = new Category;
$lifeInsurance->setName('Life Insurance');
$lifeInsurance->setMasterCategory($rainyDayFunds);
$manager->persist($lifeInsurance);

$healthInsurance = new Category;
$healthInsurance->setName('Health Insurance');
$healthInsurance->setMasterCategory($rainyDayFunds);
$manager->persist($healthInsurance);

$birthdays = new Category;
$birthdays->setName('Birthdays');
$birthdays->setMasterCategory($rainyDayFunds);
$manager->persist($birthdays);

$christmas = new Category;
$christmas->setName('Christmas');
$christmas->setMasterCategory($rainyDayFunds);
$manager->persist($christmas);

$anniversary = new Category;
$anniversary->setName('Anniversary');
$anniversary->setMasterCategory($rainyDayFunds);
$manager->persist($anniversary);

$savingsGoals = new MasterCategory;
$savingsGoals->setName('Savings Goals');
$manager->persist($savingsGoals);

$carReplacement = new Category;
$carReplacement->setName('Car Replacement');
$carReplacement->setMasterCategory($savingsGoals);
$manager->persist($carReplacement);

$vacation = new Category;
$vacation->setName('Vacation');
$vacation->setMasterCategory($savingsGoals);
$manager->persist($vacation);

$debt = new MasterCategory;
$debt->setName('Debt');
$manager->persist($debt);

$carPayment = new Category;
$carPayment->setName('Car Payment');
$carPayment->setMasterCategory($debt);
$manager->persist($carPayment);

$studentLoanPayment = new Category;
$studentLoanPayment->setName('Student Loan Payment');
$studentLoanPayment->setMasterCategory($debt);
$manager->persist($studentLoanPayment);

$personalLoanPayment = new Category;
$personalLoanPayment->setName('Personal Loan Payment');
$personalLoanPayment->setMasterCategory($debt);
$manager->persist($personalLoanPayment);

$manager->flush();
    }
}

