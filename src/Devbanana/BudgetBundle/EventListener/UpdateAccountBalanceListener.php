<?php

namespace Devbanana\BudgetBundle\EventListener;

use Devbanana\BudgetBundle\Entity\Account;
use Devbanana\BudgetBundle\Entity\LineItem;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UpdateAccountBalanceListener
{
    public function prePersist(LifecycleEventArgs $e)
    {
        $em = $e->getEntityManager();
$entity = $e->getEntity();

if ($entity instanceof LineItem) {
    $balance = $entity->getAccount()->getBalance();

    if ($entity->getInflow()) {
        $balance = bcadd($balance, $entity->getInflow(), 2);
    }
    elseif ($entity->getOutflow()) {
        $balance = bcsub($balance, $entity->getOutflow(), 2);
    }

    $entity->getAccount()->setBalance($balance);
}
    }

public function preRemove(LifecycleEventArgs $e)
    {
        $em = $e->getEntityManager();
$entity = $e->getEntity();

if ($entity instanceof LineItem) {
    $balance = $entity->getAccount()->getBalance();
        $balance = bcsub($balance, $entity->getInflow(), 2);
        $balance = bcadd($balance, $entity->getOutflow(), 2);
    $entity->getAccount()->setBalance($balance);
}
    }

}
