<?php

namespace Devbanana\BudgetBundle\EventListener;

use Devbanana\BudgetBundle\Entity\Transaction;
use Devbanana\BudgetBundle\Entity\LineItem;
use Doctrine\ORM\Event\OnFlushEventArgs;

class UpdateTransactionListener
{

public function onFlush(OnFlushEventArgs $e)
{
    $em = $e->getEntityManager();
    $uow = $em->getUnitOfWork();

    $entities = $uow->getScheduledEntityUpdates();

    foreach ($entities as $entity)
    {
if ($entity instanceof LineItem) {
    $changeSet = $uow->getEntityChangeSet($entity);
    $transaction = $entity->getTransaction();
if (isset($changeSet['inflow'])) {
    // Subtract the old value and add the new
    $inflow = $transaction->getInflow();
    $inflow = bcsub($inflow, $changeSet['inflow'][0], 2);
    $inflow = bcadd($inflow, $changeSet['inflow'][1], 2);
    $transaction->setInflow($inflow);
}
if (isset($changeSet['outflow'])) {
    // Subtract the old outflow and add the new
    $outflow = $transaction->getOutflow();
    $outflow = bcsub($outflow, $changeSet['outflow'][0], 2);
    $outflow = bcadd($outflow, $changeSet['outflow'][1], 2);
    $transaction->setOutflow($outflow);
}

$md = $em->getClassMetadata(get_class($transaction));
$uow->computeChangeSet($md, $transaction);
}
    }
}

}
