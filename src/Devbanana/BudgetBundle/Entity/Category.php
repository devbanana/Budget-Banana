<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Devbanana\BudgetBundle\Entity\CategoryRepository")
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var MasterCategory
     *
     * @ORM\ManyToOne(targetEntity="MasterCategory",
     * inversedBy="categories")
     */
    private $masterCategory;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BudgetCategories", mappedBy="category")
     */
    private $budgets;

    /**
     * How to order categories.
     *
     * @ORM\Column(name="order", type="integer")
     */
    private $order;


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
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set masterCategory
     *
     * @param \Devbanana\BudgetBundle\Entity\MasterCategory $masterCategory
     * @return Category
     */
    public function setMasterCategory(\Devbanana\BudgetBundle\Entity\MasterCategory $masterCategory = null)
    {
        $this->masterCategory = $masterCategory;

        return $this;
    }

    /**
     * Get masterCategory
     *
     * @return \Devbanana\BudgetBundle\Entity\MasterCategory 
     */
    public function getMasterCategory()
    {
        return $this->masterCategory;
    }

    /**
     * Set budgets
     *
     * @param \Devbanana\BudgetBundle\Entity\BudgetCategories $budgets
     * @return Category
     */
    public function setBudgets(\Devbanana\BudgetBundle\Entity\BudgetCategories $budgets = null)
    {
        $this->budgets = $budgets;

        return $this;
    }

    /**
     * Get budgets
     *
     * @return \Devbanana\BudgetBundle\Entity\BudgetCategories 
     */
    public function getBudgets()
    {
        return $this->budgets;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->budgets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add budgets
     *
     * @param \Devbanana\BudgetBundle\Entity\BudgetCategories $budgets
     * @return Category
     */
    public function addBudget(\Devbanana\BudgetBundle\Entity\BudgetCategories $budgets)
    {
        $this->budgets[] = $budgets;

        return $this;
    }

    /**
     * Remove budgets
     *
     * @param \Devbanana\BudgetBundle\Entity\BudgetCategories $budgets
     */
    public function removeBudget(\Devbanana\BudgetBundle\Entity\BudgetCategories $budgets)
    {
        $this->budgets->removeElement($budgets);
    }

    public function __toString()
    {
        return $this->getMasterCategory()->getName() . ' > ' . $this->getName();
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return Category
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
}
