<?php

namespace Devbanana\BudgetBundle\Entity;

use Devbanana\BudgetBundle\Entity\Account;
use Devbanana\BudgetBundle\Entity\Budget;
use Devbanana\BudgetBundle\Entity\BudgetCategories;
use Devbanana\BudgetBundle\Entity\Category;
use Devbanana\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * LineItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LineItemRepository extends EntityRepository
{

    public function queryOnOrAfter(\DateTime $date, User $user)
    {
        $qb = $this->createQueryBuilder('l')
            ->innerJoin('l.transaction', 't')
                ->where('t.date >= :date')
                ->andWhere('t.user = :user')
            ->setParameter('date', $date)
            ->setParameter('user', $user)
            ;

        return $qb;
    }

    public function findOnOrAfter(\DateTime $date, User $user)
    {
        $qb = $this->queryOnOrAfter($date);

        return $qb->getQuery()->getResult();
    }

    public function filterByBudgeted(QueryBuilder $qb)
    {
        $qb
            ->innerJoin('l.account', 'a')
            ->andWhere('a.budgeted = true')
            ;

        return $qb;
    }

    public function getBufferedIncome(Budget $budget)
    {
        $query = $this->createQueryBuilder('l')
->innerJoin('l.category', 'bc')
    ->where('bc.budget = :budget')
    ->andWhere('l.assignedMonth <> :budget')
    ->andWhere('l.type = :type')
->setParameter('budget', $budget)
->setParameter('type', 'income')
->getQuery()
;

        return $query->getResult();
    }

    public function getIncomeThisMonth(Budget $budget)
    {
        $query = $this->createQueryBuilder('l')
        ->where('l.assignedMonth = :budget')
            ->andWhere('l.type = :type')
            ->setParameter('budget', $budget)
            ->setParameter('type', 'income')
            ->select(array('l.inflow'))
            ->getQuery()
            ;

        $results = $query->getResult();

        $income = '0.00';

        foreach ($results as $result)
        {
            $income = bcadd($income, $result['inflow'], 2);
        }

        return $income;
    }

    /**
     * Get the total income assigned to months before this month.
     *
     * This month is taken as the month of the given budget.
     *
     * @param \Devbanana\BudgetBundle\Entity\Budget The budget of the
     * current month
     * @return string The total income before the month of the given budget
     */
    public function getTotalIncomeBefore(Budget $budget)
    {
        $query = $this->createQueryBuilder('l')
            ->innerJoin('l.assignedMonth', 'am')
            ->where('am.month < :month')
            ->andWhere('am.user = :user')
            ->setParameter('month', $budget->getMonth())
            ->setParameter('user', $budget->getUser())
            ->getQuery()
            ;

        $result = $query->getResult();

        $income = '0.00';

        foreach ($result as $lineItem)
        {
            $income = bcadd($income, $lineItem->getInflow(), 2);
            // Just in case we have an outflow
            $income = bcsub($income, $lineItem->getOutflow(), 2);
        }

        return $income;
    }

    public function queryByAccount(Account $account, User $user)
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.transaction', 't')
            ->where('l.account = :account')
            ->andWhere('t.user = :user')
            ->setParameter('account', $account)
            ->setParameter('user', $user)
            ->addOrderBy('l.id', 'DESC')
            ;
    }

    public function queryByCategory(Category $category)
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.category', 'bc')
            ->innerJoin('l.transaction', 't')
            ->where('bc.category = :category')
            ->setParameter('category', $category)
            ->addOrderBy('l.id', 'DESC')
            ;
    }

    /**
     * Checks whether any line item has a specified category
     * 
     * @param Category $category 
     * @access public
     * @return boolean
     */
    public function hasCategory(Category $category)
    {
        $query = $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->innerJoin('l.category', 'bc')
            ->where('bc.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ;

        return $query->getSingleScalarResult() > 0;
    }

}
