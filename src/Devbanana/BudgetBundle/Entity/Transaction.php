<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
    private $inflow;

    /**
     * @var string
     *
     * @ORM\Column(name="outflow", type="decimal", precision=14, scale=2)
     */
    private $outflow;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Subtransaction", mappedBy="transaction")
     */
    private $subtransactions;


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
        $this->subtransactions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add subtransactions
     *
     * @param \Devbanana\BudgetBundle\Entity\Subtransaction $subtransactions
     * @return Transaction
     */
    public function addSubtransaction(\Devbanana\BudgetBundle\Entity\Subtransaction $subtransactions)
    {
        $this->subtransactions[] = $subtransactions;

        return $this;
    }

    /**
     * Remove subtransactions
     *
     * @param \Devbanana\BudgetBundle\Entity\Subtransaction $subtransactions
     */
    public function removeSubtransaction(\Devbanana\BudgetBundle\Entity\Subtransaction $subtransactions)
    {
        $this->subtransactions->removeElement($subtransactions);
    }

    /**
     * Get subtransactions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubtransactions()
    {
        return $this->subtransactions;
    }
}
