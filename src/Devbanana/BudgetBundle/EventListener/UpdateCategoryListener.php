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
        $outflow += $entity->getOutflow();
        $outflow -= $entity->getInflow();

        $category->setOutflow($outflow);
        }
    }
}

}
