<?php

namespace Devbanana\BudgetBundle\EventListener;

use Devbanana\BudgetBundle\Entity\Account;
use Devbanana\BudgetBundle\Entity\LineItem;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

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

public function onFlush(OnFlushEventArgs $e)
{
    $em = $e->getEntityManager();
    $uow = $em->getUnitOfWork();

    $entities = $uow->getScheduledEntityUpdates();

    foreach ($entities as $entity)
    {
        if ($entity instanceof LineItem) {
            $changeSet = $uow->getEntityChangeSet($entity);
            $account = $entity->getAccount();
            $balance = $account->getBalance();

            if (isset($changeSet['inflow'])) {
                $balance = bcsub($balance, $changeSet['inflow'][0], 2);
                $balance = bcadd($balance, $changeSet['inflow'][1], 2);
            }
            elseif (isset($changeSet['outflow'])) {
                $balance = bcadd($balance, $changeSet['outflow'][0], 2);
                $balance = bcsub($balance, $changeSet['outflow'][1], 2);
            }

            $account->setBalance($balance);
            $md = $em->getClassMetadata(get_class($account));
            $uow->computeChangeSet($md, $account);
        }
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
