<?php

namespace Devbanana\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Account
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Devbanana\BudgetBundle\Entity\AccountRepository")
 */
class Account
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
     * @Assert\NotBlank(message="Please enter a name for the account.")
     * @Assert\Length(max=255,
     *     maxMessage="The name cannot be greater than 255 characters.")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="balance", type="decimal", precision=14, scale=2)
     * @Assert\Regex(pattern="/\d+(\.\d+)?/",
     *     message="Balance must be a number.")
     */
    private $balance = 0.00;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="LineItem", mappedBy="account")
     * @Assert\Valid
     */
    private $lineItems;

    /**
     * Asset or liability
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\Choice(choices={"asset","liability"},
     *     message="Type must be asset or liability.")
     */
    private $type;

    /**
     * Category of account.
     *
     * @ORM\ManyToOne(targetEntity="AccountCategory", inversedBy="accounts")
     * @Assert\Valid
     * @Assert\NotNull(message="Please select a category")
     */
    private $accountCategory;

    /**
     * Whether this account is budget or off-budget
     *
     * @ORM\Column(name="budgeted", type="boolean")
     */
    private $budgeted;


    public function __toString()
    {
        return $this->getName();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lineItems = new \Doctrine\Common\Collections\ArrayCollection();
        $this->balance = 0.00;
    }

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
     * @return Account
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
     * Set balance
     *
     * @param string $balance
     * @return Account
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
     * Add lineItems
     *
     * @param \Devbanana\BudgetBundle\Entity\LineItem $lineItems
     * @return Account
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

    /**
     * Set type
     *
     * @param string $type
     * @return Account
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
     * Set budgeted
     *
     * @param boolean $budgeted
     * @return Account
     */
    public function setBudgeted($budgeted)
    {
        $this->budgeted = $budgeted;

        return $this;
    }

    /**
     * Get budgeted
     *
     * @return boolean 
     */
    public function getBudgeted()
    {
        return $this->budgeted;
    }

    /**
     * Set accountCategory
     *
     * @param \Devbanana\BudgetBundle\Entity\AccountCategory $accountCategory
     * @return Account
     */
    public function setAccountCategory(\Devbanana\BudgetBundle\Entity\AccountCategory $accountCategory = null)
    {
        $this->accountCategory = $accountCategory;

        // Set default type
        $this->setType($accountCategory->getType());

        return $this;
    }

    /**
     * Get accountCategory
     *
     * @return \Devbanana\BudgetBundle\Entity\AccountCategory 
     */
    public function getAccountCategory()
    {
        return $this->accountCategory;
    }

    /**
     * Add accounts
     *
     * @param \Devbanana\BudgetBundle\Entity\Account $accounts
     * @return Account
     */
    public function addAccount(\Devbanana\BudgetBundle\Entity\Account $accounts)
    {
        $this->accounts[] = $accounts;

        return $this;
    }

    /**
     * Remove accounts
     *
     * @param \Devbanana\BudgetBundle\Entity\Account $accounts
     */
    public function removeAccount(\Devbanana\BudgetBundle\Entity\Account $accounts)
    {
        $this->accounts->removeElement($accounts);
    }

    /**
     * Get accounts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAccounts()
    {
        return $this->accounts;
    }
}
