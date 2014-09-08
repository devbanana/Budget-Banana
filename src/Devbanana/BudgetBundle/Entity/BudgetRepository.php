<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BudgetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BudgetRepository extends EntityRepository
{

    public function findOneOrCreateByDate(\DateTime $date)
    {
        $month = new \DateTime(sprintf('%04d-%02d-%02d',
                    $date->format('Y'),
                    $date->format('m'),
                    1));

        $budget = $this->findOneByMonth($month);

        if (!$budget) {
            $budget = new Budget;
            $budget->setMonth($month);
            $this->getEntityManager()->persist($budget);
            $this->getEntityManager()->flush();
        }

        return $budget;
    }

    public function findOneOrCreateByMonthAndYear($month, $year)
    {
        $date = new \DateTime(sprintf('%04d-%02d-%02d',
                    $year, $month, 1));
        return $this->findOneOrCreateByDate($date);
    }

    public function getAvailableToBudget(Budget $budget)
    {
// Get sum of all budgeted accounts
        // TODO: Accounts are not yet categorized as budgeted or not
        // budgeted yet
        $sumOfBudgetedAccounts = $this->getEntityManager()->getRepository(
                'DevbananaBudgetBundle:Account')
            ->sumBudgetedAccounts();

        // Get sum of all category balances
        $sumOfCategoryBalances = '0.00';
        foreach ($budget->getCategories() as $category)
        {
            $balance = $this->getEntityManager()->getRepository(
                    'DevbananaBudgetBundle:BudgetCategories')
                ->getBalanceForCategory($category);

            $sumOfCategoryBalances = bcadd(
                    $sumOfCategoryBalances,
                    $balance,
                    2);
        }

// Get all income received this month, applied to a later month
        $bufferedIncomeLineItems = $this->getEntityManager()->getRepository(
                'DevbananaBudgetBundle:LineItem')
            ->getBufferedIncome($budget);

$bufferedIncome = '0.00';

        foreach ($bufferedIncomeLineItems as $lineItem)
        {
$bufferedIncome = bcadd($bufferedIncome, $lineItem->getInflow());
        }

        $availableToBudget = bcsub(bcsub(
                $sumOfBudgetedAccounts,
                $sumOfCategoryBalances,
                2),
                $bufferedIncome,
                2);


        // We need to subtract all transactions on or after the date one
        // month from this budget

        $month = clone $budget->getMonth();
        $month->modify('+1 month');

            $transactions = $this->getEntityManager()->getRepository(
                    'DevbananaBudgetBundle:Transaction')
                ->findOnOrAfter($month);

            foreach ($transactions as $transaction)
            {
$availableToBudget = bcsub($availableToBudget, $transaction->getInflow(), 2);
$availableToBudget = bcadd($availableToBudget, $transaction->getOutflow(), 2);
            }

      return $availableToBudget;
    }

    public function getNotBudgetedLastMonth(Budget $budget)
    {
        $month = clone $budget->getMonth();
        $month->modify('-1 month');
        $previousBudget = $this->findOneByMonth($month);

        if ($previousBudget) {
            $availableToBudget = $this->getAvailableToBudget($previousBudget);

            return $availableToBudget;
        }
        return '0.00';
    }

    public function getOverSpentLastMonth(Budget $budget)
    {
        $month = clone $budget->getMOnth();
        $month->modify('-1 month');
        $previousBudget = $this->findOneByMonth($month);

        if ($previousBudget) {
            $overspent = '0.00';

            foreach ($previousBudget->getCategories() as $category)
            {
                if ($category->getBalance() < 0) {
                    $overspent = bcadd($overspent, $category->getBalance(), 2);
                }
            }

            return $overspent;
        }

        return '0.00';
    }

}
