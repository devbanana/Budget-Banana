<?php

namespace Devbanana\BudgetBundle\Entity;

use Devbanana\BudgetBundle\Entity\Category;
use Devbanana\BudgetBundle\Entity\MasterCategory;
use Devbanana\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{

    public function getHighestOrderFor(MasterCategory $masterCategory)
    {
        $qb = $this->createQueryBuilder('c');
            $query = $qb
                ->where($qb->expr()->eq('c.masterCategory', ':master_category'))
            ->setParameter('master_category', $masterCategory)
            ->orderBy('c.order', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ;

        $result = $query->getOneOrNullResult();

        if ($result) {
            return $result->getOrder();
        }
        else {
            return 0;
        }
    }

    public function findAllOrderedByOrder(User $user)
    {
        $query = $this->createQueryBuilder('c')
        ->innerJoin('c.masterCategory', 'mc')
            ->where('mc.user = :user')
            ->setParameter('user', $user)
            ->addOrderBy('mc.order', 'ASC')
            ->addOrderBy('c.order', 'ASC')
            ->getQuery()
            ;

        return $query->getResult();
    }

    public function reorderUp(Category $category)
    {
        // Get previously ordered category
        $query = $this->createQueryBuilder('c')
            ->where('c.masterCategory = :masterCategory')
            ->andWhere('c.order < :order')
            ->setParameter('masterCategory', $category->getMasterCategory())
            ->setParameter('order', $category->getOrder())
            ->addOrderBy('c.order', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ;

        $previousCategory = $query->getOneOrNullResult();

        if (!$previousCategory) {
            // Can't move up any further
            return false;
        }

        $prevOrder = $previousCategory->getOrder();
        $order = $category->getOrder();

        $previousCategory->setOrder($order);
        $category->setOrder($prevOrder);

        return true;
    }

    public function reorderDown(Category $category)
    {
        // Get the category that follows this one
        $query = $this->createQueryBuilder('c')
            ->where('c.masterCategory = :masterCategory')
            ->andWhere('c.order > :order')
            ->setParameter('masterCategory', $category->getMasterCategory())
            ->setParameter('order', $category->getOrder())
            ->addOrderBy('c.order', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ;

        $nextCategory = $query->getOneOrNullResult();

        if (!$nextCategory) {
            // Can't move down any further
            return false;
        }

        $nextOrder = $nextCategory->getOrder();
        $order = $category->getOrder();

        $nextCategory->setOrder($order);
        $category->setOrder($nextOrder);

        return true;
    }

}
