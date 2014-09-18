<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Devbanana\BudgetBundle\Entity\Budget;
use Devbanana\BudgetBundle\Entity\BudgetCategories;
use Devbanana\BudgetBundle\Entity\Category;

/**
 * @Route("/budget-categories")
 */
class BudgetCategoriesController extends Controller
{
    /**
     * @Route("/list/ajax/{year}/{month}",
     * name="budgetcategories_list_ajax", options={"expose":true})
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function listAjaxAction($year, $month)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $date = new \DateTime(sprintf("%04d-%02d-%02d", $year, $month, 1));

        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByDate($date, $user);

        $categories = $em->getRepository('DevbananaBudgetBundle:BudgetCategories')
            ->findOrderedCategories($budget);

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
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($content));

            return $response;
            }

    /**
     * @Route("/save/ajax/{id}/{budgeted}",
     *     name="budgetcategories_save_ajax",
     *     defaults={"budgeted"="0.00"},
     *     options={"expose":true})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function saveAjaxAction(BudgetCategories $category, $budgeted)
    {
        $this->authorizeAccess($category->getBudget());

        $em = $this->getDoctrine()->getManager();

        if (!$budgeted) {
            $budgeted = '0.00';
        }
        $category->setBudgeted($budgeted);
        $em->flush();

        $content = array();
        $content['outflow'] = $category->getOutflow();
        $content['balance'] = $category->getBalance();
        $content['budgeted'] = $budgeted;

        $response = new Response;
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($content));

        return $response;
    }

    /**
     * @Route("/toggle-carryover/ajax/{id}",
     *     name="budgetcategories_toggle_carryover_ajax",
     *     options={"expose":true})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function toggleCarryoverAjaxAction(BudgetCategories $budgetCategories)
    {
        $this->authorizeAccess($budgetCategories->getBudget());

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
$response->headers->set('Content-Type', 'application/json');
$response->setContent(json_encode($content));

return $response;
    }

    /**
     * @Route("/get-by-category/{year}/{month}/{id}",
     *     name="budgetcategories_get_by_category_ajax",
     *     options={"expose":true})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function getByCategoryAjaxAction($year, $month, Category $category)
    {
        $this->authorizeAccess($category->getMasterCategory());

        $em = $this->getDoctrine()->getManager();
        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByMonthAndYear($month, $year, $this->getUser());

        $budgetCategories = $em->getRepository('DevbananaBudgetBundle:BudgetCategories')
            ->findOneBy(array(
                        'budget' => $budget,
                        'category' => $category,
                        ));

        $content = array();
        $content['id'] = $budgetCategories->getId();

        $response = new Response;
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($content));

        return $response;
    }

    /**
     * @Route("/get/assigned-months/{year}/{month}",
     *     name="budgetcategories_get_assigned_months_ajax",
     *     options={"expose":true})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function getAssignedMonthsAjaxAction($year, $month)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $budget = $em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByMonthAndYear($month, $year, $user);

        $months = array();
        $months[] = $budget;
            $month = clone $budget->getMonth();

        for ($i = 0; $i < 59; $i++)
        {
$month->modify('+1 month');
$budget = $em->getRepository('DevbananaBudgetBundle:Budget')
    ->findOneOrCreateByDate($month, $user);
$months[] = $budget;
        }

        $content = array();
        $content['assignedMonths'] = array();

        foreach ($months as $month)
        {
            $content['assignedMonths'][] = array(
                    'id' => $month->getId(),
                    'month' => 'Income for ' . $month->getMonth()->format('F Y'),
                    );
        }

        $response = new Response;
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($content));

        return $response;
    }

    private function authorizeAccess($entity)
    {
        if ($entity->getUser() != $this->getUser()
                && !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->createAccessDeniedException();
        }
    }

}
