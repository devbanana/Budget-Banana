<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PayeeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PayeeRepository extends EntityRepository
{
    public function findAllOrderedByName()
    {
        $q = $this->createQueryBuilder('p')
            ->orderBy('p.name', 'ASC')
            ->getQuery();

        return $q->getResult();
    }
}