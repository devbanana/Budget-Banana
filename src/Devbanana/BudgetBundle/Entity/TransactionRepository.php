<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TransactionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionRepository extends EntityRepository
{

    public function findOnOrAfter(\DateTime $date)
    {
        $qb = $this->createQueryBuilder('t');
        $query = $qb
            ->where($qb->expr()->gte('t.date', ':date'))
            ->setParameter('date', $date)
            ->getQuery()
            ;

        return $query->getResult();
    }

    public function findBetween(\DateTime $month1, \DateTime $month2)
    {
        $qb = $this->createQueryBuilder('t');
        $query = $qb
            ->where($qb->expr()->andX(
                        $qb->expr()->gte('t.date', ':month1'),
                        $qb->expr()->lt('t.date', ':month2')
                        ))
            ->setParameter('month1', $month1)
            ->setParameter('month2', $month2)
            ->getQuery()
            ;

        return $query->getResult();
    }

}
