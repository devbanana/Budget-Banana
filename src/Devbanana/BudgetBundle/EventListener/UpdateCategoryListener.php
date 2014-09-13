<?php

namespace Devbanana\BudgetBundle\EventListener;

use Devbanana\BudgetBundle\Entity\BudgetCategories;
use Devbanana\BudgetBundle\Entity\Category;
use Devbanana\BudgetBundle\Entity\LineItem;
use Devbanana\BudgetBundle\Entity\MasterCategory;
use Devbanana\BudgetBundle\Entity\Transaction;
use Devbanana\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class UpdateCategoryListener
{
    public function preUpdate(PreUpdateEventArgs $e)
    {
        $em = $e->getEntityManager();
$entity = $e->getEntity();

if ($entity instanceof BudgetCategories) {
    // When a BudgetCategories entity is updated, it means the budgeted
    // field is updated and so we need to refresh the balance
    $balance = $em->getRepository('DevbananaBudgetBundle:BudgetCategories')
    ->getBalanceForCategory($entity);

    $entity->setBalance($balance);
}
    }

public function onFlush(OnFlushEventArgs $e)
{
    $em = $e->getEntityManager();
    $uow = $em->getUnitOfWork();

    $entities = $uow->getScheduledEntityUpdates();

    foreach ($entities as $entity)
    {
if ($entity instanceof LineItem) {
    $changeSet = $uow->getEntityChangeSet($entity);
    $category = $entity->getCategory();
    if ($category) {
        $outflow = $category->getOutflow();
if (isset($changeSet['inflow'])) {
    // Subtract the old value and add the new
    $outflow = bcsub($outflow, $changeSet['inflow'][0], 2);
    $outflow = bcadd($outflow, $changeSet['inflow'][1], 2);
}
if (isset($changeSet['outflow'])) {
    // Add the old value and subtract the new
    $outflow = bcadd($outflow, $changeSet['outflow'][0], 2);
    $outflow = bcsub($outflow, $changeSet['outflow'][1], 2);
}
$category->setOutflow($outflow);
$md = $em->getClassMetadata(get_class($category));
$uow->computeChangeSet($md, $category);
}
}
    }
}

public function prePersist(LifecycleEventArgs $e)
{
    $em = $e->getEntityManager();
    $entity = $e->getEntity();

    if ($entity instanceof LineItem) {
        $category = $entity->getCategory();

        if ($category) {
        $outflow = $em->getRepository('DevbananaBudgetBundle:BudgetCategories')
            ->getOutflowForCategory($category);
        $outflow = bcsub($outflow, $entity->getOutflow(), 2);
        $outflow = bcadd($outflow, $entity->getInflow(), 2);

        $category->setOutflow($outflow);
        }
    }
    elseif ($entity instanceof Category) {
        // When a category is created, create BudgetCategories for each
        // month
        $budgets = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findByUser($entity->getMasterCategory()->getUser());

        foreach ($budgets as $budget)
        {
            // Create new BudgetCategories
            $budgetCategories = new BudgetCategories;
            $budgetCategories->setBudget($budget);
            $budgetCategories->setCategory($entity);
            $budgetCategories->setOrder($entity->getOrder());
            $budgetCategories->setCarryOver($entity->getCarryOver());
            $em->persist($budgetCategories);
        }

    }
    elseif ($entity instanceof User) {
        $this->createUserCategories($entity, $em);
    }
}

public function preRemove(LifecycleEventArgs $e)
{
    $em = $e->getEntityManager();
    $entity = $e->getEntity();

    if ($entity instanceof LineItem) {
        $this->updateTransactionBalanceWhenLineItemIsDeleted($entity, $em);
        $this->updateCategoryBalanceWhenTransactionIsDeleted($entity, $em);
    }
}

private function updateTransactionBalanceWhenLineItemIsDeleted(
        LineItem $entity,
        EntityManager $em)
{
$entity->getTransaction()->setOutflow(
        bcsub(
            $entity->getTransaction()->getOutflow(),
            $entity->getOutflow(),
            2
            ));

$entity->getTransaction()->setInflow(bcsub(
            $entity->getTransaction()->getInflow(),
            $entity->getInflow(),
            2
            ));
}

private function updateCategoryBalanceWhenTransactionIsDeleted(
        LineItem $entity, EntityManager $em)
{
        $category = $entity->getCategory();

        if ($category) {
        $outflow = $em->getRepository('DevbananaBudgetBundle:BudgetCategories')
            ->getOutflowForCategory($category);
        $outflow = bcadd($outflow, $entity->getOutflow(), 2);
        $outflow = bcsub($outflow, $entity->getInflow(), 2);

        $category->setOutflow($outflow);
        }
}

private function createUserCategories(User $user, EntityManager $em)
    {
        $masterOrder = 0;
        $order = 0;

$giving = new MasterCategory;
$giving->setName('Giving');
$giving->setOrder($masterOrder++);
$giving->setUser($user);
$em->persist($giving);

$tithing = new Category;
$tithing->setName('Tithing');
$tithing->setOrder($order++);
$tithing->setMasterCategory($giving);
$em->persist($tithing);

$charitable = new Category;
$charitable->setName('Charitable');
$charitable->setOrder($order++);
$charitable->setMasterCategory($giving);
$em->persist($charitable);

$monthlyBills = new MasterCategory;
$monthlyBills->setOrder($masterOrder++);
$monthlyBills->setName('Monthly Bills');
$monthlyBills->setUser($user);
$em->persist($monthlyBills);

$rentMortgage = new Category;
$rentMortgage->setName('Rent/Mortgage');
$rentMortgage->setOrder($order++);
$rentMortgage->setMasterCategory($monthlyBills);
$em->persist($rentMortgage);

$phone = new Category;
$phone->setName('Phone');
$phone->setOrder($order++);
$phone->setMasterCategory($monthlyBills);
$em->persist($phone);

$internet = new Category;
$internet->setName('Internet');
$internet->setOrder($order++);
$internet->setMasterCategory($monthlyBills);
$em->persist($internet);

$cableTv = new Category;
$cableTv->setName('Cable TV');
$cableTv->setOrder($order++);
$cableTv->setMasterCategory($monthlyBills);
$em->persist($cableTv);

$electricity = new Category;
$electricity->setName('Electricity');
$electricity->setOrder($order++);
$electricity->setMasterCategory($monthlyBills);
$em->persist($electricity);

$water = new Category;
$water->setName('Water');
$water->setMasterCategory($monthlyBills);
$water->setOrder($order++);
$em->persist($water);

$gas = new Category;
$gas->setName('Natural Gas/Propane/Oil');
$gas->setOrder($order++);
$gas->setMasterCategory($monthlyBills);
$em->persist($gas);

$everydayExpenses = new MasterCategory;
$everydayExpenses->setName('Everyday Expenses');
$everydayExpenses->setOrder($masterOrder++);
$everydayExpenses->setUser($user);
$em->persist($everydayExpenses);

$groceries = new Category;
$groceries->setName('Groceries');
$groceries->setOrder($order++);
$groceries->setMasterCategory($everydayExpenses);
$em->persist($groceries);

$fuel = new Category;
$fuel->setName('Fuel');
$fuel->setOrder($order++);
$fuel->setMasterCategory($everydayExpenses);
$em->persist($fuel);

$money = new Category;
$money->setName('Spending Money');
$money->setOrder($order++);
$money->setMasterCategory($everydayExpenses);
$em->persist($money);

$restaurants = new Category;
$restaurants->setName('Restaurants');
$restaurants->setOrder($order++);
$restaurants->setMasterCategory($everydayExpenses);
$em->persist($restaurants);

$medical = new Category;
$medical->setName('Medical');
$medical->setOrder($order++);
$medical->setMasterCategory($everydayExpenses);
$em->persist($medical);

$clothing = new Category;
$clothing->setName('Clothing');
$clothing->setMasterCategory($everydayExpenses);
$clothing->setOrder($order++);
$em->persist($clothing);

$householdGoods = new Category;
$householdGoods->setName('Household Goods');
$householdGoods->setOrder($order++);
$householdGoods->setMasterCategory($everydayExpenses);
$em->persist($householdGoods);

$rainyDayFunds = new MasterCategory;
$rainyDayFunds->setName('Rainy Day Funds');
$rainyDayFunds->setOrder($masterOrder++);
$rainyDayFunds->setUser($user);
$em->persist($rainyDayFunds);

$emergencyFund = new Category;
$emergencyFund->setName('Emergency Fund');
$emergencyFund->setOrder($order++);
$emergencyFund->setMasterCategory($rainyDayFunds);
$em->persist($rainyDayFunds);

$carRepairs = new Category;
$carRepairs->setName('Car Repairs');
$carRepairs->setOrder($order++);
$carRepairs->setMasterCategory($rainyDayFunds);
$em->persist($rainyDayFunds);

$homeMaintenance = new Category;
$homeMaintenance->setName('Home Maintenance');
$homeMaintenance->setOrder($order++);
$homeMaintenance->setMasterCategory($rainyDayFunds);
$em->persist($homeMaintenance);

$carInsurance = new Category;
$carInsurance->setName('Car Insurance');
$carInsurance->setOrder($order++);
$carInsurance->setMasterCategory($rainyDayFunds);
$em->persist($rainyDayFunds);

$lifeInsurance = new Category;
$lifeInsurance->setName('Life Insurance');
$lifeInsurance->setOrder($order++);
$lifeInsurance->setMasterCategory($rainyDayFunds);
$em->persist($lifeInsurance);

$healthInsurance = new Category;
$healthInsurance->setName('Health Insurance');
$healthInsurance->setOrder($order++);
$healthInsurance->setMasterCategory($rainyDayFunds);
$em->persist($healthInsurance);

$birthdays = new Category;
$birthdays->setName('Birthdays');
$birthdays->setOrder($order++);
$birthdays->setMasterCategory($rainyDayFunds);
$em->persist($birthdays);

$christmas = new Category;
$christmas->setName('Christmas');
$christmas->setOrder($order++);
$christmas->setMasterCategory($rainyDayFunds);
$em->persist($christmas);

$anniversary = new Category;
$anniversary->setName('Anniversary');
$anniversary->setOrder($order++);
$anniversary->setMasterCategory($rainyDayFunds);
$em->persist($anniversary);

$savingsGoals = new MasterCategory;
$savingsGoals->setName('Savings Goals');
$savingsGoals->setOrder($masterOrder++);
$savingsGoals->setUser($user);
$em->persist($savingsGoals);

$carReplacement = new Category;
$carReplacement->setName('Car Replacement');
$carReplacement->setOrder($order++);
$carReplacement->setMasterCategory($savingsGoals);
$em->persist($carReplacement);

$vacation = new Category;
$vacation->setName('Vacation');
$vacation->setOrder($order++);
$vacation->setMasterCategory($savingsGoals);
$em->persist($vacation);

$debt = new MasterCategory;
$debt->setName('Debt');
$debt->setOrder($masterOrder++);
$debt->setUser($user);
$em->persist($debt);

$carPayment = new Category;
$carPayment->setName('Car Payment');
$carPayment->setMasterCategory($debt);
$carPayment->setOrder($order++);
$em->persist($carPayment);

$studentLoanPayment = new Category;
$studentLoanPayment->setName('Student Loan Payment');
$studentLoanPayment->setOrder($order++);
$studentLoanPayment->setMasterCategory($debt);
$em->persist($studentLoanPayment);

$personalLoanPayment = new Category;
$personalLoanPayment->setName('Personal Loan Payment');
$personalLoanPayment->setOrder($order++);
$personalLoanPayment->setMasterCategory($debt);
$em->persist($personalLoanPayment);
    }

}
