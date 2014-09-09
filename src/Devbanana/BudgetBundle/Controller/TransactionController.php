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
     * @Route("/{year}/{month}", name="transactions_index",
     *     defaults={"year" = null, "month" = null})
     * @Method("GET")
     * @Template()
     */
    public function indexAction($year, $month)
    {
        if (!$month) $month = date('n');
        if (!$year) $year = date('Y');

        $em = $this->getDoctrine()->getManager();

        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByMonthAndYear($month, $year);

        $startMonth = clone $budget->getMonth();
        $endMonth = clone $startMonth;
        $endMonth->modify('+1 month');

        $entities = $em->getRepository('DevbananaBudgetBundle:Transaction')
            ->findBetween($startMonth, $endMonth);

        $lastMonth = clone $startMonth;
        $lastMonth->modify('-1 month');

        return array(
                'entities' => $entities,
                'lastMonth' => $lastMonth,
                'nextMonth' => $endMonth,
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
    // For transfers, create an equivalent transaction on the opposite end
$lineItems = $transaction->getLineItems();
foreach ($lineItems as $lineItem)
{
    if ($lineItem->getType() == 'transfer') {
        $newLineItem = new LineItem;
        $newLineItem->setType('transfer');
        $newLineItem->setAccount($lineItem->getTransferAccount());
        $newLineItem->setTransferAccount($lineItem->getAccount());
        // TODO: Deal with categories for off-budget accounts
if (bccomp($lineItem->getInflow(), '0.00', 2)) {
    $newLineItem->setOutflow($lineItem->getInflow());
}
if (bccomp($lineItem->getOutflow(), '0.00', 2)) {
    $newLineItem->setInflow($lineItem->getOutflow());
}
$transaction->addLineItem($newLineItem);
    }
}

    $em->persist($transaction);
    $em->flush();
            return $this->redirect($this->generateUrl('transactions_index'));
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

}
