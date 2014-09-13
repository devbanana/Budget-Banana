<?php

namespace Devbanana\BudgetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Devbanana\UserBundle\Entity\User;
use Devbanana\BudgetBundle\Entity\Budget;

class TransactionType extends AbstractType
{

    private $user;

    private $budget;

    public function __construct(User $user, Budget $budget)
{
    $this->user = $user;
    $this->budget = $budget;
}

        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array(
                        'format' => \IntlDateFormatter::FULL,
                        'years' => range(date('Y'), date('Y')+1),
                        'error_bubbling' => true,
                        ))
            ->add('lineitems', 'collection', array(
                        'type' => new LineItemType($this->user, $this->budget),
                        'allow_add' => true,
                        'by_reference' => false,
                        'error_bubbling' => true,
                        ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Devbanana\BudgetBundle\Entity\Transaction',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'devbanana_budgetbundle_transaction';
    }
}
