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
                        'empty_value' => '',
                        'property' => 'choiceString',
                        ))
            ->add('payee', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Payee',
                        'query_builder' => function (EntityRepository $er)
                        {
                        return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                        },
                        'error_bubbling' => true,
                        'empty_value' => '',
                        ))
            ->add('payer', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Payer',
                        'error_bubbling' => true,
                        'empty_value' => '',
                        'query_builder' => function (EntityRepository $er)
                        {
                        return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                        },
                        ))
            ->add('transferAccount', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Account',
                        'error_bubbling' => true,
                        'empty_value' => '',
                        ))
            ->add('category', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:BudgetCategories',
                        'error_bubbling' => true,
                        'empty_value' => '',
                        'property' => 'choiceString',
                        'query_builder' => function (EntityRepository $er)
                        {
$qb = $er->createQueryBuilder('bc');
$qb->innerJoin('bc.category', 'c')
->innerJoin('c.masterCategory', 'mc');
if ($this->budget) {
$qb->where($qb->expr()->eq('bc.budget', ':budget'))
->setParameter('budget', $this->budget);
}
$qb->addOrderBy('mc.order', 'ASC')
->addOrderBy('c.order', 'ASC');
return $qb;
                        },
                        ))
            ->add('assignedMonth', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Budget',
                        'error_bubbling' => true,
                        'empty_value' => true,
                        'query_builder' => function (EntityRepository $er)
                        {
                        $startMonth = $this->budget->getMonth();
                        $endMonth = clone $startMonth;
                        $endMonth->modify('+59 months');
                        $qb = $er->createQueryBuilder('am');
                        return $qb
                        ->where($qb->expr()->gte('am.month', ':startMonth'))
                        ->andWhere($qb->expr()->lte('am.month', ':endMonth'))
                            ->setParameter('startMonth', $startMonth)
                            ->setParameter('endMonth', $endMonth)
                        ->orderBy('am.month', 'ASC');
                        }
                        ))
            ->add('inflow', 'money', array(
                        'currency' => 'USD',
                        'error_bubbling' => true,
                        'required' => false,
                        ))
            ->add('outflow', 'money', array(
                        'currency' => 'USD',
                        'error_bubbling' => true,
                        'required' => false,
                        ))
            ->add('checkNumber', 'text', array(
                        'error_bubbling' => true,
                        'required' => false,
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
