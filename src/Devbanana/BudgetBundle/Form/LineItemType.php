<?php

namespace Devbanana\BudgetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LineItemType extends AbstractType
{
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
                        'required' => true,
                        ))
            ->add('account')
            ->add('payee')
            ->add('category', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:BudgetCategories',
                        'empty_value' => '',
                        'choices' => array(),
                        ))
            ->add('inflow', 'money', array(
                        'currency' => 'USD',
                        ))
            ->add('outflow', 'money', array(
                        'currency' => 'USD',
                        ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Devbanana\BudgetBundle\Entity\LineItem'
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
