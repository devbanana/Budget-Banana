<?php

namespace Devbanana\BudgetBundle\EventListener;

use Devbanana\BudgetBundle\Entity\LineItem;
use Devbanana\BudgetBundle\Entity\BudgetCategories;
use Devbanana\BudgetBundle\Entity\Category;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;

class UpdateCategoryListener
{
    public function preUpdate(LifecycleEventArgs $e)
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
        bcadd(
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
