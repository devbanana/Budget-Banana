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
            $this->getManager()->persist($budget);
            $this->getManager()->flush();
        }

        return $budget;
    }

}