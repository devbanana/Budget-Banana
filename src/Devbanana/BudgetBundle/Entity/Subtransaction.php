<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subtransaction
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Devbanana\BudgetBundle\Entity\SubtransactionRepository")
 */
class Subtransaction
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="inflow", type="decimal")
     */
    private $inflow;

    /**
     * @var string
     *
     * @ORM\Column(name="outflow", type="decimal")
     */
    private $outflow;

    /**
     * @var Transaction
     *
     * @ORM\ManyToOne(targetEntity="Transaction",
     * inversedBy="subtransactions")
     */
    private $transaction;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="subtransactions")
     */
    private $account;


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
     * Set type
     *
     * @param string $type
     * @return Subtransaction
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set inflow
     *
     * @param string $inflow
     * @return Subtransaction
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
     * @return Subtransaction
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
     * Set transaction
     *
     * @param \Devbanana\BudgetBundle\Entity\Transaction $transaction
     * @return Subtransaction
     */
    public function setTransaction(\Devbanana\BudgetBundle\Entity\Transaction $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \Devbanana\BudgetBundle\Entity\Transaction 
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set account
     *
     * @param \Devbanana\BudgetBundle\Entity\Account $account
     * @return Subtransaction
     */
    public function setAccount(\Devbanana\BudgetBundle\Entity\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \Devbanana\BudgetBundle\Entity\Account 
     */
    public function getAccount()
    {
        return $this->account;
    }
}
