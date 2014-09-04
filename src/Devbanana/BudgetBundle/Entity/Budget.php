<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Budget
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Devbanana\BudgetBundle\Entity\BudgetRepository")
 */
class Budget
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="month", type="datetime")
     */
    private $month;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set month
     *
     * @param \DateTime $month
     * @return Budget
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return \DateTime 
     */
    public function getMonth()
    {
        return $this->month;
    }
}
