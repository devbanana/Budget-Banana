<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function newAction(Request $request)
    {
        $session = $request->getSession();

        $year = date('Y');
        $month = date('n');
        $day = date('j');

        if ($session->has('new-transaction-day')) {
            $day = $session->get('new-transaction-day');

            if ($session->has('new-transaction-month')) {
                $month = $session->get('new-transaction-month');

                if ($session->has('new-transaction-year')) {
                    $year = $session->get('new-transaction-year');
                }
            }
        }

        $date = new \DateTime(
                sprintf('%04d-%02d-%02d',
                    $year,
                    $month,
                    $day
                    ));
        
        $transaction = new Transaction;
        $transaction->setDate($date);
        $li1 = new LineItem;
        $transaction->getLineItems()->add($li1);

        $em = $this->getDoctrine()->getManager();
        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByDate($transaction->getDate());

        $form = $this->createForm(new TransactionType(), $transaction, array(
                    'budget' => $budget,
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
    public function indexAction(Request $request, $year, $month)
    {
        $session = $request->getSession();
        if ($month) {
            $session->set('transaction-month', $month);
        }
        else {
            if ($session->has('transaction-month')) {
                $month = $session->get('transaction-month');
            }
            else {
                $month = date('n');
            }
        }

        if ($year) {
            $session->set('transaction-year', $year);
        }
        else {
            if ($session->has('transaction-year')) {
                $year = $session->get('transaction-year');
            }
            else {
                $year = date('Y');
            }
        }

        $em = $this->getDoctrine()->getManager();

        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByMonthAndYear($month, $year);

        $thisMonth = $budget->getMonth();

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
                'thisMonth' => $thisMonth,
                'nextMonth' => $endMonth,
            );    }

        /**
         * @Route("/", name="transactions_create_ajax",
         *     options={"expose":true})
         * @Method("POST")
         */
        public function createAjaxAction(Request $request)
        {
            $session = $request->getSession();
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
$year = $transactions['date']['year'];
$month = $transactions['date']['month'];

$budget = $em->getRepository('DevbananaBudgetBundle:Budget')
->findOneOrCreateByMonthAndYear($month, $year);

$form = $this->createForm(new TransactionType(), $transaction, array(
            'budget' => $budget,
            ));
$form->handleRequest($request);

$response = new Response;
$response->headers->set('Content-Type', 'Application/JSON');
$content = array();

if ($form->isValid()) {

    // Save our date to session
    $date = $transaction->getDate();
    $session->set('new-transaction-year', $date->format('Y'));
            $session->set('new-transaction-month', $date->format('n'));
            $session->set('new-transaction-day', $date->format('j'));

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

    $content['success'] = true;
    $content['inflow'] = $transaction->getInflow();
    $content['outflow'] = $transaction->getOutflow();

    // generate CSRF token
$csrf = $this->get('security.csrf.token_manager');
$token = $csrf->refreshToken('');
$content['csrf'] = "$token";
}
else {
    $content['success'] = false;
    $content['errors'] = array();
    foreach ($form->getErrors() as $error)
    {
        $content['errors'][] = $error->getMessage();
    }
}

$response->setContent(json_encode($content));

return $response;
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
