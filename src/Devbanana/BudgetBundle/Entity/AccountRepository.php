<?php

namespace Devbanana\BudgetBundle\Entity;

use Devbanana\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * AccountRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountRepository extends EntityRepository
{

    public function getNetWorth(User $user)
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.balance')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ;

        $result = $query->getResult();

        $netWorth = '0.00';

        foreach ($result as $account)
        {
            $netWorth = bcadd($netWorth, $account['balance'], 2);
        }

        return $netWorth;
    }

}
