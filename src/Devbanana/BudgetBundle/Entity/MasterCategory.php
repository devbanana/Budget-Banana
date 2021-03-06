<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MasterCategory
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Devbanana\BudgetBundle\Entity\MasterCategoryRepository")
 */
class MasterCategory
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Category", mappedBy="masterCategory")
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $categories;

    /**
     * How to order the master category
     *
     * @ORM\Column(name="sortOrder", type="integer")
     */
    private $order;

    /**
     * The user that created this master category
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
     * Set name
     *
     * @param string $name
     * @return MasterCategory
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
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add categories
     *
     * @param \Devbanana\BudgetBundle\Entity\Category $categories
     * @return MasterCategory
     */
    public function addCategory(\Devbanana\BudgetBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Devbanana\BudgetBundle\Entity\Category $categories
     */
    public function removeCategory(\Devbanana\BudgetBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return MasterCategory
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

    /**
     * Set user
     *
     * @param \Devbanana\UserBundle\Entity\User $user
     * @return MasterCategory
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
