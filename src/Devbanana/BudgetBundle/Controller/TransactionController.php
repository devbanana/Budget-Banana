<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/new", name="transactions_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $transaction = new Transaction;
        $transaction->setDate(new \DateTime(date('Y-m-d', time())));
        $li1 = new LineItem;
        $transaction->getLineItems()->add($li1);

        $form = $this->createForm(new TransactionType(), $transaction, array(
                    'action' => $this->generateUrl('transactions_create'),
                    ));
        $form->add('submit', 'submit', array(
                    'label' => 'Add',
                    ));

        return array(
                'form' => $form->createView(),
            );    }

        /**
         * @Route("/", name="transactions_create")
         * @Method("POST")
         * @Template("DevbananaBudgetBundle:Transaction:new.html.twig")
         */
        public function createAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();

            $transactions = $request->request->get('devbanana_budgetbundle_transaction');
foreach ($transactions['lineitems'] as $i => $lineitem)
{
    if (isset($lineitem['assignedMonth'])) {
        list($year, $month) = explode('-', $lineitem['assignedMonth']);
        $date = new \DateTime(sprintf('%04d-%02d-%02d', $year, $month, 1));

        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneByMonth($date);

        if (!$budget) {
            $budget = new Budget;
            $budget->setMonth($date);
            $em->persist($budget);
            $em->flush();
        }

        $transactions['lineitems'][$i]['assignedMonth'] = $budget->getId();
    }
}

$request->request->set('devbanana_budgetbundle_transaction', $transactions);

$transaction = new Transaction;
$form = $this->createForm(new TransactionType(), $transaction)
    ->add('submit', 'submit');
$form->handleRequest($request);

if ($form->isValid()) {
    $em->persist($transaction);
    $em->flush();
            return $this->redirect($this->generateUrl('transactions_list'));
}

return array(
        'entity' => $transaction,
        'form' => $form->createView(),
        );
        }

    /**
     * @Route("/{id}", name="transactions_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Transaction $transaction)
    {
        return array(
                'entity' => $transaction,
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
