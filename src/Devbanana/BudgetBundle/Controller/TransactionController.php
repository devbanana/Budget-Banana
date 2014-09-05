<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devbanana\BudgetBundle\Form\TransactionType;
use Devbanana\BudgetBundle\Form\LineItemType;
use Devbanana\BudgetBundle\Entity\Transaction;
use Devbanana\BudgetBundle\Entity\LineItem;

/**
 * @Route("/transactions")
 */
class TransactionController extends Controller
{
    /**
     * @Route("/", name="transactions_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('DevbananaBudgetBundle:Transaction')
            ->findAll();

        return array(
                'entities' => $entities,
            );    }

    /**
     * @Route("/new", name="transactions_create")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $transaction = new Transaction;
        $transaction->setDate(new \DateTime(date('Y-m-d', time())));
        $li1 = new LineItem;
        $transaction->getLineItems()->add($li1);

        $form = $this->createForm(new TransactionType(), $transaction);
        $form->add('submit', 'submit', array(
                    'label' => 'Add',
                    ));

        return array(
                'form' => $form->createView(),
            );    }

    /**
     * @Route("/{id}", name="transactions_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        return array(
            );    }

    /**
     * @Route("/transactions/{id}/edit")
     * @Template()
     */
    public function editAction($id)
    {
        return array(
                // ...
            );    }

    /**
     * @Route("/transactions/{id}/delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        return array(
                // ...
            );    }

}
