<?php

namespace Devbanana\BudgetBundle\EventListener;

use Devbanana\BudgetBundle\Entity\Budget;
use Devbanana\BudgetBundle\Entity\BudgetCategories;
use Doctrine\ORM\Event\OnFlushEventArgs;

class PopulateBudgetCategoriesListener
{
    public function onFlush(OnFlushEventArgs $e)
    {
        $em = $e->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = $uow->getScheduledEntityInsertions();

        foreach ($entities as $entity)
        {
            if (!($entity instanceof Budget)) {
                continue;
            }

$mainCategories = $em->getRepository('DevbananaBudgetBundle:Category')
    ->findAllOrderedByOrder($entity->getUser());

foreach ($mainCategories as $category)
{
$budgetCategories = $this->getNewBudgetCategories();
$entity->addCategory($budgetCategories);
$budgetCategories->setCategory($category);
$budgetCategories->setOrder($category->getOrder());
$budgetCategories->setCarryOver($category->getCarryOver());
$em->persist($budgetCategories);

$md = $em->getClassMetadata('Devbanana\BudgetBundle\Entity\BudgetCategories');
$uow->computeChangeSet($md, $budgetCategories);
}
        }
    }

protected function getNewBudgetCategories()
{
    return new BudgetCategories;
}
}
