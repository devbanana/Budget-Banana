<?php

namespace Devbanana\BudgetBundle\EventListener;

use Devbanana\BudgetBundle\Entity\LineItem;
use Devbanana\BudgetBundle\Entity\BudgetCategories;
use Doctrine\ORM\Event\LifecycleEventArgs;

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
        $outflow = bcadd($outflow, $entity->getOutflow(), 2);
        $outflow = bcsub($outflow, $entity->getInflow(), 2);

        $category->setOutflow($outflow);
        }
    }
}

public function preRemove(LifecycleEventArgs $e)
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
}

}
