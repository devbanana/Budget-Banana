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
        $balance += $entity->getInflow();
    }
    elseif ($entity->getOutflow()) {
        $balance -= $entity->getOutflow();
    }

    $entity->getAccount()->setBalance($balance);
}
    }

}
