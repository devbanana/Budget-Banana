<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="lineItems")
     * @Assert\NotNull(message="Please select an account.")
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(name="inflow", type="decimal", precision=14, scale=2)
     * @Assert\GreaterThanOrEqual(value="0.00",
     *     message="Inflow must not be negative.")
     */
    private $inflow = 0.00;

    /**
     * @var string
     *
     * @ORM\Column(name="outflow", type="decimal", precision=14, scale=2)
     * @Assert\GreaterThanOrEqual(value="0.00",
     *     message="Outflow must not be negative.")
     */
    private $outflow = 0.00;

    /**
     * @var string
     *
     * @ORM\Column(name="memo", type="string", length=255, nullable=true)
     */
    private $memo;

    /**
     * @var Transaction
     *
     * @ORM\ManyToOne(targetEntity="Transaction",
     * inversedBy="lineItems")
     */
    private $transaction;

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
     * @var \Devbanana\BudgetBundle\Entity\Budget
     *
     * @ORM\ManyToOne(targetEntity="Budget")
     */
    private $assignedMonth;

    /**
     * The account to transfer to.
     *
     * @ORM\ManyToOne(targetEntity="Account")
     */
    private $transferAccount;

    /**
     * The check number of this line item
     *
     * @ORM\Column(name="checkNumber", type="integer", nullable=true)
     */
    private $checkNumber;


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
        if ($inflow) {
            $this->inflow = $inflow;
        }

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
        if ($outflow) {
            $this->outflow = $outflow;
        }

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

    /**
     * Set assigned_month
     *
     * @param \Devbanana\BudgetBundle\Entity\Budget $assignedMonth
     * @return LineItem
     */
    public function setAssignedMonth(\Devbanana\BudgetBundle\Entity\Budget $assignedMonth = null)
    {
        $this->assignedMonth = $assignedMonth;

        return $this;
    }

    /**
     * Get assigned_month
     *
     * @return \Devbanana\BudgetBundle\Entity\Budget 
     */
    public function getAssignedMonth()
    {
        return $this->assignedMonth;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return LineItem
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Get memo
     *
     * @return string 
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Set transferAccount
     *
     * @param \Devbanana\BudgetBundle\Entity\Account $transferAccount
     * @return LineItem
     */
    public function setTransferAccount(\Devbanana\BudgetBundle\Entity\Account $transferAccount = null)
    {
        $this->transferAccount = $transferAccount;

        return $this;
    }

    /**
     * Get transferAccount
     *
     * @return \Devbanana\BudgetBundle\Entity\Account 
     */
    public function getTransferAccount()
    {
        return $this->transferAccount;
    }

    /**
     * Set checkNumber
     *
     * @param integer $checkNumber
     * @return LineItem
     */
    public function setCheckNumber($checkNumber)
    {
        $this->checkNumber = $checkNumber;

        return $this;
    }

    /**
     * Get checkNumber
     *
     * @return integer 
     */
    public function getCheckNumber()
    {
        return $this->checkNumber;
    }
}
