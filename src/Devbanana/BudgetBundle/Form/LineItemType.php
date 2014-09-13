<?php

namespace Devbanana\BudgetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Devbanana\UserBundle\Entity\User;
use Devbanana\BudgetBundle\Entity\Budget;

class LineItemType extends AbstractType
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
                        'query_builder' => function (EntityRepository $er)
                        {
                        $qb = $er->createQueryBuilder('a');
                        return $qb
                        ->where($qb->expr()->eq('a.user', ':user'))
                        ->setParameter('user', $this->user)
                        ;
                        }
                        ))
            ->add('payee', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Payee',
                        'query_builder' => function (EntityRepository $er)
                        {
                        return $er->createQueryBuilder('p')
                        ->where('p.user = :user')
                        ->setParameter('user', $this->user)
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
                        ->where('p.user = :user')
                        ->setParameter('user', $this->user)
                        ->orderBy('p.name', 'ASC');
                        },
                        ))
            ->add('transferAccount', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Account',
                        'error_bubbling' => true,
                        'empty_value' => '',
                        'query_builder' => function (EntityRepository $er)
                        {
                        return $er->createQueryBuilder('a')
                        ->where('a.user = :user')
                        ->setParameter('user', $this->user)
                        ->orderBy('a.name');
                        }
                        ))
            ->add('category', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:BudgetCategories',
                        'error_bubbling' => true,
                        'empty_value' => '',
                        'property' => 'choiceString',
                        'query_builder' => function (EntityRepository $er)
                        {
return $er->createQueryBuilder('bc')
->innerJoin('bc.budget', 'b')
->innerJoin('bc.category', 'c')
->innerJoin('c.masterCategory', 'mc')
->where('bc.budget = :budget')
->setParameter('budget', $this->budget)
->andWhere('b.user = :user')
->setParameter('user', $this->user)
->addOrderBy('mc.order', 'ASC')
->addOrderBy('c.order', 'ASC');
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
                        return $er->createQueryBuilder('am')
                        ->where('am.user = :user')
                        ->andWhere('am.month >= :startMonth')
                        ->andWhere('am.month < :endMonth')
                        ->setParameter('user', $this->user)
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
