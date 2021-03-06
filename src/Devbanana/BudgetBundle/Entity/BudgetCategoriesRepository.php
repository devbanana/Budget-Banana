<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BudgetCategoriesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BudgetCategoriesRepository extends EntityRepository
{

    public function getOutflowForCategory(BudgetCategories $category)
    {
        $query = $this->createQueryBuilder('bc')
            ->innerJoin('bc.lineItems', 'l')
                ->innerJoin('bc.budget', 'b')
            ->select('l.outflow')
            ->where('l.category = :category')
            ->andWhere('b.user = :user')
            ->setParameter('category', $category)
            ->setParameter('user', $category->getBudget()->getUser())
            ->getQuery()
            ;

        $outflow = '0.00';

        foreach ($query->getResult() as $lineItem)
        {
            $outflow = bcsub($outflow, $lineItem['outflow'], 2);
        }

   return $outflow;
    }

    public function getBalanceForCategory($category)
    {
if ($category instanceof BudgetCategories) {
    $budgeted = $category->getBudgeted();

$previousCategory = $this->getPreviousMonthCategory($category);
$previousBalance = $this->getBalanceForCategory($previousCategory);

// If carry over is default of budget, then only use balance if positive.
if ($previousBalance < 0 && $previousCategory->getCarryOver() == 'budget') {
$previousBalance = '0.00';
}

// NOTE: We must use bcadd here because outflow is always negative
return bcadd(bcadd($previousBalance, $budgeted, 2),
        $category->getOutflow(),
        2);
}

return '0.00';
    }

public function getPreviousMonthCategory(BudgetCategories $category)
{
    $date = clone $category->getBudget()->getMonth();
    $date->modify('-1 month');

    $query = $this->createQueryBuilder('bc')
    ->innerJoin('bc.budget', 'b')
    ->where('bc.category = :category')
                ->andWhere('b.month = :month')
                ->andWhere('b.user = :user')
    ->setParameter('category', $category->getCategory())
    ->setParameter('month', $date)
    ->setParameter('user', $category->getBudget()->getUser())
    ->getQuery()
    ;

$previousMonth = $query->getResult();

if ($previousMonth) {
    return $previousMonth[0];
}
return null;
}

public function getNextMonthCategory(BudgetCategories $category)
{
    $date = clone $category->getBudget()->getMonth();
    $date->modify('+1 month');

    $query = $this->createQueryBuilder('bc')
    ->innerJoin('bc.budget', 'b')
        ->where('bc.category = :category')
        ->andWhere('b.month = :month')
        ->andWhere('b.user = :user')
    ->setParameter('category', $category->getCategory())
    ->setParameter('month', $date)
    ->setParameter('user', $category->getBudget()->getUser())
    ->getQuery()
    ;

$nextMonth = $query->getResult();

if ($nextMonth) {
    return $nextMonth[0];
}
return null;
}

public function getBudgetedThisMonth(Budget $budget)
{
    $query = $this->createQueryBuilder('bc')
    ->where('bc.budget = :budget')
        ->setParameter('budget', $budget)
        ->select('bc.budgeted')
        ->getQuery()
        ;

    $results = $query->getResult();

    $budgeted = '0.00';

    foreach ($results as $result)
    {
        $budgeted = bcadd($budgeted, $result['budgeted'], 2);
    }

    return $budgeted;
}

/**
 * Get the total budgeted before this month.
 *
 * This month is the                 month of the given budget.
 *
 * @param \Devbanana\BudgetBundle\Entity\Budget The current month's budget
 * @return string The total budgeted before the current month
 */
public function getTotalBudgetedBefore(Budget $budget)
{
    $query = $this->createQueryBuilder('bc')
        ->innerJoin('bc.budget', 'b')
        ->where('b.month < :month')
        ->andWhere('b.user = :user')
        ->setParameter('month', $budget->getMonth())
        ->setParameter('user', $budget->getUser())
        ->getQuery()
        ;

    $result = $query->getResult();

    $budgeted = '0.00';

    foreach ($result as $budgetCategories)
    {
        $budgeted = bcadd($budgeted, $budgetCategories->getBudgeted(), 2);
    }

    return $budgeted;
}

public function findBalanceByCategory(Category $category)
{
    $month = new \DateTime(
            sprintf('%04d-%02d-%02d',
                date('Y'), date('n'), 1
                ));
    $query = $this->createQueryBuilder('bc')
        ->select('bc.balance')
        ->innerJoin('bc.budget', 'b')
        ->where('bc.category = :category')
        ->andWhere('b.month = :month')
        ->setParameter('category', $category)
        ->setParameter('month', $month)
        ->getQuery()
        ;

        return $query->getSingleScalarResult();
}

public function findOrderedCategories(Budget $budget)
{
    $query = $this->createQueryBuilder('bc')
->innerJoin('bc.budget', 'b')
->innerJoin('bc.category', 'c')
->innerJoin('c.masterCategory', 'mc')
->where('bc.budget = :budget')
->andWhere('b.user = :user')
->setParameter('budget', $budget)
->setParameter('user', $budget->getUser())
->addOrderBy('mc.order', 'ASC')
->addOrderBy('c.order', 'ASC')
->getQuery()
;

    return $query->getResult();
}

}
