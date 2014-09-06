<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LineItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Devbanana\BudgetBundle\Entity\LineItemRepository")
 */
class LineItem
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
     * inversedBy="lineItems")
     */
    private $transaction;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="lineItems")
     */
    private $account;

    /**
     * @var Payee
     *
     * @ORM\ManyToOne(targetEntity="Payee", inversedBy="lineItems")
     */
    private $payee;

    /**
     * @var Payer
     *
     * @ORM\ManyToOne(targetEntity="Payer", inversedBy="lineItems")
     */
    private $payer;

    /**
     * @var BudgetCategory
     *
     * @ORM\ManyToOne(targetEntity="BudgetCategories", inversedBy="lineItems")
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
     * Set type
     *
     * @param string $type
     * @return LineItem
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
     * @return LineItem
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
     * @return LineItem
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
     * @return LineItem
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
     * @return LineItem
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

    /**
     * Set payee
     *
     * @param \Devbanana\BudgetBundle\Entity\Payee $payee
     * @return LineItem
     */
    public function setPayee(\Devbanana\BudgetBundle\Entity\Payee $payee = null)
    {
        $this->payee = $payee;

        return $this;
    }

    /**
     * Get payee
     *
     * @return \Devbanana\BudgetBundle\Entity\Payee 
     */
    public function getPayee()
    {
        return $this->payee;
    }

    /**
     * Set payer
     *
     * @param \Devbanana\BudgetBundle\Entity\Payer $payer
     * @return LineItem
     */
    public function setPayer(\Devbanana\BudgetBundle\Entity\Payer $payer = null)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * Get payer
     *
     * @return \Devbanana\BudgetBundle\Entity\Payer 
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * Set category
     *
     * @param \Devbanana\BudgetBundle\Entity\BudgetCategories $category
     * @return LineItem
     */
    public function setCategory(\Devbanana\BudgetBundle\Entity\BudgetCategories $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Devbanana\BudgetBundle\Entity\BudgetCategories 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
