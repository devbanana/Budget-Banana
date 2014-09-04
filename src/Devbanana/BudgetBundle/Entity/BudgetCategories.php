<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BudgetCategories
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Devbanana\BudgetBundle\Entity\BudgetCategoriesRepository")
 */
class BudgetCategories
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
     * @var string
     *
     * @ORM\Column(name="budgeted", type="decimal", precision=14, scale=2)
     */
    private $budgeted;

    /**
     * @var string
     *
     * @ORM\Column(name="outflow", type="decimal", precision=14, scale=2)
     */
    private $outflow;

    /**
     * @var string
     *
     * @ORM\Column(name="balance", type="decimal", precision=14, scale=2)
     */
    private $balance;

    /**
     * @var Budget
     *
     * @ORM\ManyToOne(targetEntity="Budget", inversedBy="categories")
     */
    private $budget;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="budgets")
     */
    private $category;


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
     * Set budgeted
     *
     * @param string $budgeted
     * @return BudgetCategories
     */
    public function setBudgeted($budgeted)
    {
        $this->budgeted = $budgeted;

        return $this;
    }

    /**
     * Get budgeted
     *
     * @return string 
     */
    public function getBudgeted()
    {
        return $this->budgeted;
    }

    /**
     * Set outflow
     *
     * @param string $outflow
     * @return BudgetCategories
     */
    public function setOutflow($outflow)
    {
        $this->outflow = $outflow;

        return $this;
    }

    /**
     * Get outflow
     *
     * @return string 
     */
    public function getOutflow()
    {
        return $this->outflow;
    }

    /**
     * Set balance
     *
     * @param string $balance
     * @return BudgetCategories
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return string 
     */
    public function getBalance()
    {
        return $this->balance;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->budget = new \Doctrine\Common\Collections\ArrayCollection();
        $this->category = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add budget
     *
     * @param \Devbanana\BudgetBundle\Entity\Budget $budget
     * @return BudgetCategories
     */
    public function addBudget(\Devbanana\BudgetBundle\Entity\Budget $budget)
    {
        $this->budget[] = $budget;

        return $this;
    }

    /**
     * Remove budget
     *
     * @param \Devbanana\BudgetBundle\Entity\Budget $budget
     */
    public function removeBudget(\Devbanana\BudgetBundle\Entity\Budget $budget)
    {
        $this->budget->removeElement($budget);
    }

    /**
     * Get budget
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Add category
     *
     * @param \Devbanana\BudgetBundle\Entity\Category $category
     * @return BudgetCategories
     */
    public function addCategory(\Devbanana\BudgetBundle\Entity\Category $category)
    {
        $this->category[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Devbanana\BudgetBundle\Entity\Category $category
     */
    public function removeCategory(\Devbanana\BudgetBundle\Entity\Category $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Get category
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set budget
     *
     * @param \Devbanana\BudgetBundle\Entity\Budget $budget
     * @return BudgetCategories
     */
    public function setBudget(\Devbanana\BudgetBundle\Entity\Budget $budget = null)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Set category
     *
     * @param \Devbanana\BudgetBundle\Entity\Category $category
     * @return BudgetCategories
     */
    public function setCategory(\Devbanana\BudgetBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }
}
