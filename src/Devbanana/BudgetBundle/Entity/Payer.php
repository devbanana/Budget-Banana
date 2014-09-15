<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Payer
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Devbanana\BudgetBundle\Entity\PayerRepository")
 */
class Payer
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
     * @Assert\NotBlank(message="Please enter a name for the payer.")
     * @Assert\Length(max=255,
     *     maxMessage="The name cannot be greater than 255 characters.")
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LineItem", mappedBy="payer")
     */
    private $lineItems;

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
     * @return Payer
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
        $this->lineItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add lineItems
     *
     * @param \Devbanana\BudgetBundle\Entity\LineItem $lineItems
     * @return Payer
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
        return $this->getName();
    }

    /**
     * Set user
     *
     * @param \Devbanana\UserBundle\Entity\User $user
     * @return Payer
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
