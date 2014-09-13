<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Budget
 *
 * @ORM\Table(indexes={@ORM\index(name="month_idx", columns={"month"})})
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
     * @ORM\Column(name="month", type="date")
     */
    private $month;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BudgetCategories", mappedBy="budget")
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $categories;

    /**
     * The user this budget belongs to.
     *
     * @ORM\ManyToOne(targetEntity="Devbanana\UserBundle\Entity\User")
     */
    private $user;


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

    /**
     * Set categories
     *
     * @param \Devbanana\BudgetBundle\Entity\BudgetCategories $categories
     * @return Budget
     */
    public function setCategories(\Devbanana\BudgetBundle\Entity\BudgetCategories $categories = null)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return \Devbanana\BudgetBundle\Entity\BudgetCategories 
     */
    public function getCategories()
    {
        return $this->categories;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add categories
     *
     * @param \Devbanana\BudgetBundle\Entity\BudgetCategories $categories
     * @return Budget
     */
    public function addCategory(\Devbanana\BudgetBundle\Entity\BudgetCategories $categories)
    {
        $this->categories[] = $categories;
        $categories->setBudget($this);

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Devbanana\BudgetBundle\Entity\BudgetCategories $categories
     */
    public function removeCategory(\Devbanana\BudgetBundle\Entity\BudgetCategories $categories)
    {
        $this->categories->removeElement($categories);
    }

    public function __toString()
    {
        return $this->getMonth()->format('F, Y');
    }




    /**
     * Set user
     *
     * @param \Devbanana\UserBundle\Entity\User $user
     * @return Budget
     */
    public function setUser(\Devbanana\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Devbanana\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
