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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LineItem", mappedBy="category")
     */
    private $lineItems;


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
     * Constructor
     */
    public function __construct()
    {
        $this->budgeted = 0.00;
        $this->outflow = 0.00;
        $this->balance = 0.00;
        $this->lineItems = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get budget
     *
     * @return \Devbanana\BudgetBundle\Entity\Budget 
     */
    public function getBudget()
    {
        return $this->budget;
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

    /**
     * Get category
     *
     * @return \Devbanana\BudgetBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add lineItems
     *
     * @param \Devbanana\BudgetBundle\Entity\LineItem $lineItems
     * @return BudgetCategories
     */
    public function addLineItem(\Devbanana\BudgetBundle\Entity\LineItem $lineItems)
    {
        $this->lineItems[] = $lineItems;

        return $this;
    }

    /**
     * Remove lineItems
     *
     * @param \Devbanana\BudgetBundle\Entity\LineItem $lineItems
     */
    public function removeLineItem(\Devbanana\BudgetBundle\Entity\LineItem $lineItems)
    {
        $this->lineItems->removeElement($lineItems);
    }

    /**
     * Get lineItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLineItems()
    {
        return $this->lineItems;
    }

    public function __toString()
    {
        return $this->category->__toString();
    }
}