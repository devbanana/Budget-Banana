<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Transaction
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Devbanana\BudgetBundle\Entity\TransactionRepository")
 */
class Transaction
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
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="inflow", type="decimal", precision=14, scale=2)
     */
    private $inflow = 0.00;

    /**
     * @var string
     *
     * @ORM\Column(name="outflow", type="decimal", precision=14, scale=2)
     */
    private $outflow = 0.00;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LineItem", mappedBy="transaction",
     * cascade={"persist"})
     * @Assert\Valid
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
     * Set date
     *
     * @param \DateTime $date
     * @return Transaction
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set inflow
     *
     * @param string $inflow
     * @return Transaction
     */
    public function setInflow($inflow)
    {
        $this->inflow = $inflow;

        return $this;
    }

    /**
     * Get inflow
     *
     * @return string 
     */
    public function getInflow()
    {
        return $this->inflow;
    }

    /**
     * Set outflow
     *
     * @param string $outflow
     * @return Transaction
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
     * @return Transaction
     */
    public function addLineItem(\Devbanana\BudgetBundle\Entity\LineItem $lineItems)
    {
        $this->lineItems[] = $lineItems;
        $lineItems->setTransaction($this);

        if ($lineItems->getInflow()) {
            $this->inflow += $lineItems->getInflow();
        }
        if ($lineItems->getOutflow()) {
            $this->outflow += $lineItems->getOutflow();
        }

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

    /**
     * Set user
     *
     * @param \Devbanana\UserBundle\Entity\User $user
     * @return Transaction
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
