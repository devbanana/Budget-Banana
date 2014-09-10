<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devbanana\BudgetBundle\Entity\Budget;
use Devbanana\BudgetBundle\Entity\BudgetCategories;

/**
 * @Route("/budget-categories")
 */
class BudgetCategoriesController extends Controller
{
    /**
     * @Route("/list/ajax/{year}/{month}",
     * name="budgetcategories_list_ajax", options={"expose":true})
     * @Method("POST")
     */
    public function listAjaxAction($year, $month)
    {
        $em = $this->getDoctrine()->getManager();

        $date = new \DateTime(sprintf("%04d-%02d-%02d", $year, $month, 1));

        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneByMonth($date);

        if (!$budget) {
            $budget = new Budget;
            $budget->setMonth($date);
            $em->persist($budget);
            $em->flush();
        }

            $categories = $budget->getCategories();

            $content = array();
            $content['categories'] = array();

            foreach ($categories as $category)
            {
                $content['categories'][] = array(
                        'id' => $category->getId(),
                        'name' => "$category",
                        'balance' => $category->getBalance(),
                        );
            }

            $response = new Response;
            $response->headers->set('Content-Type', 'Application/JSON');
            $response->setContent(json_encode($content));

            return $response;
            }

    /**
     * @Route("/save/ajax/{id}/{budgeted}",
     *     name="budgetcategories_save_ajax",
     *     options={"expose":true})
     */
    public function saveAjaxAction(BudgetCategories $category, $budgeted)
    {
        $em = $this->getDoctrine()->getManager();

        $category->setBudgeted($budgeted);
        $em->flush();

        $content = array();
        $content['outflow'] = $category->getOutflow();
        $content['balance'] = $category->getBalance();

        $response = new Response;
        $response->headers->set('Content-Type', 'Application/JSON');
        $response->setContent(json_encode($content));

        return $response;
    }

    /**
     * @Route("/toggle-carryover/ajax/{id}",
     *     name="budgetcategories_toggle_carryover_ajax",
     *     options={"expose":true})
     */
    public function toggleCarryoverAjaxAction(BudgetCategories $budgetCategories)
    {
        $em = $this->getDoctrine()->getManager();

        $carryOver = $budgetCategories->getCarryOver();

        if ($carryOver == 'budget') {
            $carryOver = 'category';
        }
        else {
            $carryOver = 'budget';
        }

        $budgetCategories->setCarryOver($carryOver);
        $budgetCategories->getCategory()->setCarryOver($carryOver);

        while ($budgetCategories = $em->getRepository(
                    'DevbananaBudgetBundle:BudgetCategories')
                ->getNextMonthCategory($budgetCategories))
        {
            $budgetCategories->setCarryOver($carryOver);
        }

        $em->flush();

$content = array();
$content['carryOver'] = ucwords($carryOver);

$response = new Response;
$response->headers->set('Content-Type', 'Application/JSON');
$response->setContent(json_encode($content));

return $response;
    }

}
