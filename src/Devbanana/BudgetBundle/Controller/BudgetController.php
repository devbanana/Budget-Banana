<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devbanana\BudgetBundle\Form\BudgetType;
use Devbanana\BudgetBundle\Form\BudgetCategoriesType;
use Devbanana\BudgetBundle\Entity\Budget;

/**
 * @Route("/budget")
 */
class BudgetController extends Controller
{

    /**
     * @Route("/calculate/ajax/{id}",
     *     name="budget_calculate_ajax",
     *     options={"expose":true})
     */
    public function calculateAjaxAction(Budget $budget)
    {
$em = $this->getDoctrine()->getManager();

$content = array();

foreach ($budget->getCategories() as $category)
{
    $item = array();
    $item['id'] = $category->getId();
$item['outflow'] = $em->getRepository('DevbananaBudgetBundle:BudgetCategories')
    ->getOutflowForCategory($category);
$item['balance'] = $em->getRepository(
        'DevbananaBudgetBundle:BudgetCategories')
    ->getBalanceForCategory($category);
$content['categories'][] = $item;
}

$em->flush();

$response = new Response;
$response->headers->set('Content-Type', 'Application/JSON');
$response->setContent(json_encode($content));

return $response;
    }

        /**
         * @Route("/available-to-budget/{id}",
         *     name="budget_available_to_budget",
         *     options={"expose":true})
         */
        public function getAvailableToBudgetAction(Budget $budget)
        {
            $em = $this->getDoctrine()->getManager();

            $availableToBudget = $em->getRepository('DevbananaBudgetBundle:Budget')
                ->getAvailableToBudget($budget);

            $content = array();
            $content['availableToBudget'] = $availableToBudget;

$response = new Response;
$response->headers->set('Content-Type', 'Application/JSON');
$response->setContent(json_encode($content));

return $response;
        }

/**
 * @Route("/overspent-last-month/ajax/{id}",
 *     name="budget_overspent_last_month_ajax",
 *     options={"expose":true})
 */
public function getOverspentLastMonth(Budget $budget)
        {
            $em = $this->getDoctrine()->getManager();

            $overspentLastMonth = $em->getRepository('DevbananaBudgetBundle:Budget')
                ->getOverspentLastMonth($budget);

    $month = clone $budget->getMonth();
    $month->modify('-1 month');

            $content = array();
            $content['overspentLastMonth'] = $overspentLastMonth;
            $content['month'] = $month->format('F');

$response = new Response;
$response->headers->set('Content-Type', 'Application/JSON');
$response->setContent(json_encode($content));

return $response;
        }

/**
 * @Route("/not-budgeted-last-month/ajax/{id}",
 *     name="budget_not_budgeted_last_month_ajax",
 *     options={"expose":true})
 */
public function getNotBudgetedLastMonthAjaxAction(Budget $budget)
{
    $em = $this->getDoctrine()->getManager();

    $notBudgetedLastMonth = $em->getRepository('DevbananaBudgetBundle:Budget')
        ->getNotBudgetedLastMonth($budget);

    $month = clone $budget->getMonth();
    $month->modify('-1 month');

    $content = array();
    $content['notBudgetedLastMonth'] = $notBudgetedLastMonth;
    $content['month'] = $month->format('F');

    $response = new Response;
    $response->headers->set('Content-Type', 'Application/JSON');
    $response->setContent(json_encode($content));

    return $response;
}

/**
 * @Route("/income-this-month/ajax/{id}",
 *     name="budget_income_this_month_ajax",
 *     options={"expose":true})
 */
public function getIncomeThisMonthAjaxAction(Budget $budget)
        {
            $em = $this->getDoctrine()->getManager();

            $incomeThisMonth = $em->getRepository('DevbananaBudgetBundle:LineItem')
                ->getIncomeThisMonth($budget);

    $month = $budget->getMonth();

            $content = array();
            $content['incomeThisMonth'] = $incomeThisMonth;
            $content['month'] = $month->format('F');

$response = new Response;
$response->headers->set('Content-Type', 'Application/JSON');
$response->setContent(json_encode($content));

return $response;
        }

/**
 * @Route("/budgeted-this-month/ajax/{id}",
 *     name="budget_budgeted_this_month_ajax",
 *     options={"expose":true})
 */
public function getBudgetedThisMonthAjaxAction(Budget $budget)
        {
            $em = $this->getDoctrine()->getManager();

            $budgetedThisMonth = $em->getRepository(
                    'DevbananaBudgetBundle:BudgetCategories')
                ->getBudgetedThisMonth($budget);

    $month = $budget->getMonth();

            $content = array();
            $content['budgetedThisMonth'] = $budgetedThisMonth;
            $content['month'] = $month->format('F');

$response = new Response;
$response->headers->set('Content-Type', 'Application/JSON');
$response->setContent(json_encode($content));

return $response;
        }


    /**
     * @Route("/{year}/{month}", name="budget",
     *     defaults={"year" = null, "month" = null})
     * @Template()
     */
    public function budgetAction($year, $month)
    {
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }

        $em = $this->getDoctrine()->getManager();

        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByMonthAndYear($month, $year);

        $lastMonth = clone $budget->getMonth();
        $lastMonth->modify('-1 month');
        $previousBudget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByDate($lastMonth);

        $nextMonth = clone $budget->getMonth();
        $nextMonth->modify('+1 month');
        $nextBudget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByDate($nextMonth);

        $form = $this->createForm(new BudgetType(), $budget);

        return array(
                'form' => $form->createView(),
                'entity' => $budget,
                'previousBudget' => $previousBudget,
                'nextBudget' => $nextBudget,
            );    }

}
