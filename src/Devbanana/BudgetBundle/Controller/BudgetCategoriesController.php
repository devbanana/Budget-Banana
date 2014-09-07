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
 * @Route("/budget")
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
                        );
            }

            $response = new Response;
            $response->headers->set('Content-Type', 'Application/JSON');
            $response->setContent(json_encode($content));

            return $response;
            }

}