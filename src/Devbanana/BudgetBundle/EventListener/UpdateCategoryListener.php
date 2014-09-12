<?php

namespace Devbanana\BudgetBundle\EventListener;

use Devbanana\BudgetBundle\Entity\Transaction;
use Devbanana\BudgetBundle\Entity\LineItem;
use Devbanana\BudgetBundle\Entity\BudgetCategories;
use Devbanana\BudgetBundle\Entity\Category;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;

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
            ->findAll();

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

}
