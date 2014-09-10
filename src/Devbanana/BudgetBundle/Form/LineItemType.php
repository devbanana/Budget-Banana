<?php

namespace Devbanana\BudgetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class LineItemType extends AbstractType
{

    private $budget;

    public function __construct($budget = null)
    {
        $this->budget = $budget;
    }

        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                        'choices' => array(
                            'expense' => 'Expense',
                            'income' => 'Income',
                            'transfer' => 'Transfer',
                            ),
                        'error_bubbling' => true,
                        'required' => true,
                        ))
            ->add('account', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Account',
                        'error_bubbling' => true,
                        ))
            ->add('payee', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Payee',
                        'error_bubbling' => true,
                        ))
            ->add('payer', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Payer',
                        'error_bubbling' => true,
                        ))
            ->add('transferAccount', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Account',
                        'error_bubbling' => true,
                        ))
            ->add('category', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:BudgetCategories',
                        'error_bubbling' => true,
                        'query_builder' => function (EntityRepository $er)
                        {
$qb = $er->createQueryBuilder('bc');
if ($this->budget) {
$qb->where($qb->expr()->eq('bc.budget', ':budget'))
->setParameter('budget', $this->budget);
}
$qb->orderBy('bc.order', 'ASC');
return $qb;
                        },
                        ))
            ->add('assignedMonth', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Budget',
                        'error_bubbling' => true,
                        ))
            ->add('inflow', 'money', array(
                        'currency' => 'USD',
                        'error_bubbling' => true,
                        ))
            ->add('outflow', 'money', array(
                        'currency' => 'USD',
                        'error_bubbling' => true,
                        ))
            ->add('memo', 'text', array(
                        'error_bubbling' => true,
                        'required' => false,
                        ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Devbanana\BudgetBundle\Entity\LineItem',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'devbanana_budgetbundle_lineitem';
    }
}
