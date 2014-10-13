<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Devbanana\UserBundle\Entity\User;

/**
 * BudgetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BudgetRepository extends EntityRepository
{

    public function findOneOrCreateByDate(\DateTime $date, User $user, $flush = true)
    {
        $month = new \DateTime(sprintf('%04d-%02d-%02d',
                    $date->format('Y'),
                    $date->format('m'),
                    1));

        $budget = $this->createQueryBuilder('b')
            ->where('b.month = :month')
            ->andWhere('b.user = :user')
            ->setParameter('month', $month)
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;

        if (!$budget) {
            $budget = new Budget;
            $budget->setMonth($month);
            $budget->setUser($user);
            $this->getEntityManager()->persist($budget);
            if ($flush) $this->getEntityManager()->flush();
        }

        return $budget;
    }

    public function findOneOrCreateByMonthAndYear($month, $year, User $user, $flush = true)
    {
        $date = new \DateTime(sprintf('%04d-%02d-%02d',
                    $year, $month, 1));
        return $this->findOneOrCreateByDate($date, $user, $flush);
    }

    /**
     * Calculates Available to Budget for a given budget entity
     *
     * Available to Budget is the total amount that the user can put towards
     * their budget categories.
     *
     * The formula is:
     * Available to Budget
     *     = Not Budgeted Last Month
     *     - Overspent last month
     *     + Income assigned to this month
     *     - Amount budgeted this month
     * 
     * So as an example, if this month you receive $1,000 income that you
     * assign to this month, you budget $700 of that income, and last month
     * there was $200 you did not budget, but overspent in a few categories
     * totalling $100, the Available to Budget would be:
     *
     *     $200.00 (Not Budgeted Last Month)
     *     - $100.00 (Overspent last month)
     *     + $1,000.00 (Income assigned this month)
     *     - $700.00 (Amount budgeted this month)
     *     = $400.00 (Available to Budget)
     *
     * @param \Devbanana\BudgetBundle\Entity\Budget The budget this month
     *     @return string The amount available to budget
     */
    public function getAvailableToBudget(Budget $budget)
    {

        // 1. Not budgeted last month
        $notBudgetedLastMonth = $this->getNotBudgetedLastMonth($budget);

        // 2. - Overspent last month
        $overspentLastMonth = $this->getOverSpentLastMonth($budget);

        // 3. + Income assigned this month
        $incomeThisMonth = $this->getEntityManager()
            ->getRepository('DevbananaBudgetBundle:LineItem')
            ->getIncomeThisMonth($budget);

        // 4. - Budgeted this month
        $budgetedThisMonth = $this->getEntityManager()
            ->getRepository('DevbananaBudgetBundle:BudgetCategories')
            ->getBudgetedThisMonth($budget);

        // Now calculate using bcmath for accuracy
        $availableToBudget = bcsub($notBudgetedLastMonth,
                $overspentLastMonth,
                2);
        $availableToBudget = bcadd($availableToBudget, $incomeThisMonth, 2);
        $availableToBudget = bcsub($availableToBudget, $budgetedThisMonth, 2);

      return $availableToBudget;
    }

    /**
     * Get the amount not budgeted last month
     *
     * This can really be calculated as all income before this month minus
     * total amount budgeted before this month.
     *
     * @param \Devbanana\BudgetBundle\Entity\Budget The budget this month
     * @return string The amount not budgeted last month
     */
    public function getNotBudgetedLastMonth(Budget $budget)
    {
        $lastMonth = clone $budget->getMonth();
        $lastMonth->modify('-1 month');

        $lastMonthBudget = $this->findOneBy(array(
                    'month' => $lastMonth,
                    'user' => $budget->getUser(),
                    ));

        if ($lastMonthBudget) {
            return $this->getAvailableToBudget($lastMonthBudget);
        }
        else {
            return '0.00';
        }
    }

    public function getOverSpentLastMonth(Budget $budget)
    {
        $month = clone $budget->getMonth();
        $month->modify('-1 month');
        $previousBudget = $this->findOneBy(array(
                    'month' => $month,
                    'user' => $budget->getUser(),
                    ));

        if ($previousBudget) {
            $overspent = '0.00';

            foreach ($previousBudget->getCategories() as $category)
            {
                if (bccomp($category->getBalance(), '0.00', 2) < 0) {
                    $overspent = bcsub($overspent, $category->getBalance(), 2);
                }
            }

            return $overspent;
        }

        return '0.00';
    }

}
