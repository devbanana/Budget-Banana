<?php

namespace Devbanana\BudgetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TransactionType extends AbstractType
{
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
                        'type' => new LineItemType(array($options['budget'])),
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
            'budget' => null,
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
