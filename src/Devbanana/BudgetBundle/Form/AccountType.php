<?php

namespace Devbanana\BudgetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AccountType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Account'))
            ->add('startingBalance', 'money', array(
                        'label' => 'Starting Balance',
                        'currency' => 'USD',
                        'grouping' => true,
                        'mapped' => false,
                        ))
            ->add('accountCategory', 'entity', array(
                        'label' => 'Account Type',
                        'class' => 'DevbananaBudgetBundle:AccountCategory',
                        'empty_value' => '',
                        ))
            ->add('budgeted', 'choice', array(
                        'label' => 'On or Off Budget?',
                        'choices' => array(
                            1 => 'On Budget',
                            0 => 'Off Budget',
                            ),
                        ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Devbanana\BudgetBundle\Entity\Account'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'devbanana_budgetbundle_account';
    }
}
