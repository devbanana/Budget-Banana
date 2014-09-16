<?php

namespace Devbanana\BudgetBundle\Controller;

use Devbanana\BudgetBundle\Entity\Account;
use Devbanana\BudgetBundle\Entity\LineItem;
use Devbanana\BudgetBundle\Entity\Transaction;
use Devbanana\BudgetBundle\Form\LineItemType;
use Devbanana\BudgetBundle\Form\TransactionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/transactions")
 */
class TransactionController extends Controller
{
    /**
     * @Route("/new/{account_id}",
     *     name="transactions_new",
     *     defaults={"account_id" = null})
     * @Method("GET")
     * @Template()
     * @ParamConverter("account", options={"id" = "account_id"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function newAction(Request $request, Account $account = null)
    {
        $user = $this->getUser();

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
        $transaction->setUser($user);
        $li1 = new LineItem;
        $li1->setAccount($account);
        $transaction->getLineItems()->add($li1);

        $em = $this->getDoctrine()->getManager();
        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByDate($transaction->getDate(), $user, false);

        // Make sure there are 60 months of budgets to be selected for income
        $this->createBudgets($em, $budget->getMonth());

        $form = $this->createForm(new TransactionType($user, $budget),
                $transaction);

        return array(
                'form' => $form->createView(),
                'entity' => $transaction,
            );    }

        /**
         * @Route("/", name="transactions_create_ajax",
         *     options={"expose":true})
         * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
         */
        public function createAjaxAction(Request $request)
        {
            $session = $request->getSession();
            $em = $this->getDoctrine()->getManager();

            $transactionArray = $request->request->get('devbanana_budgetbundle_transaction');

$transaction = new Transaction;
$transaction->setUser($this->getUser());

$year = $transactionArray['date']['year'];
$month = $transactionArray['date']['month'];

$budget = $em->getRepository('DevbananaBudgetBundle:Budget')
->findOneOrCreateByMonthAndYear($month, $year, $this->getUser());

$form = $this->createForm(new TransactionType($this->getUser(), $budget), $transaction);
$form->handleRequest($request);

$response = new Response;
$response->headers->set('Content-Type', 'application/json');
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
     * @Route("/{id}/edit", name="transactions_edit")
     * @Template("DevbananaBudgetBundle:Transaction:new.html.twig")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function editAction(Transaction $transaction)
    {
        $this->authorizeAccess($transaction);

        $em = $this->getDoctrine()->getManager();
        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByDate($transaction->getDate(), $this->getUser(), false);

        // Make sure there are 60 months of budgets to be selected for income
        $this->createBudgets($em, $budget->getMonth());

        $form = $this->createForm(new TransactionType($this->getUser(), $budget), $transaction, array(
                    'action' => $this->generateUrl('transactions_update_ajax', array('id' => $transaction->getId())),
                    ));

        return array(
                'form' => $form->createView(),
                'entity' => $transaction,
            );    }

        /**
         * @Route("/{id}/update",
         *     name="transactions_update_ajax",
         *     options={"expose":true})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
         */
        public function updateActionAjax(Request $request, Transaction $transaction)
{
    $this->authorizeAccess($transaction);

        $em = $this->getDoctrine()->getManager();
        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByDate($transaction->getDate(), $this->getUser());

        $form = $this->createForm(new TransactionType($this->getUser(), $budget), $transaction);
        $form->handleRequest($request);

        $content = array();

        if ($form->isValid()) {
            $em->flush();
            $content['success'] = true;
            $content['redirect'] = $this->generateUrl('transactions_index');
        }
        else {
            $content['success'] = false;
        }

        $response = new Response;
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($content));

        return $response;
}

    /**
     * @Route("/{id}", name="transactions_show")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function showAction(Transaction $transaction)
    {
        $this->authorizeAccess($transaction);
        return array(
                'entity' => $transaction,
            );    }

    /**
     * @Route("/{year}/{month}", name="transactions_index",
     *     defaults={"year" = null, "month" = null})
     * @Method("GET")
     * @Template()
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
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

        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByMonthAndYear($month, $year, $user);

        $thisMonth = $budget->getMonth();

        $startMonth = clone $budget->getMonth();
        $endMonth = clone $startMonth;
        $endMonth->modify('+1 month');

        $entities = $em->getRepository('DevbananaBudgetBundle:Transaction')
            ->findBetween($startMonth, $endMonth, $user);

        $lastMonth = clone $startMonth;
        $lastMonth->modify('-1 month');

        return array(
                'entities' => $entities,
                'lastMonth' => $lastMonth,
                'thisMonth' => $thisMonth,
                'nextMonth' => $endMonth,
            );    }

        private function createBudgets(\Doctrine\ORM\EntityManager $em, $startMonth)
{
    $month = clone $startMonth;
for ($i = 0; $i < 59; $i++)
{
    $month->modify('+1 month');
    $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
        ->findOneOrCreateByDate($month, $this->getUser(), false);
}

$em->flush();
}

private function authorizeAccess(Transaction $transaction)
{
    if ($transaction->getUser() != $this->getUser()
            && !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
        $this->createAccessDeniedException();
    }
}

}
