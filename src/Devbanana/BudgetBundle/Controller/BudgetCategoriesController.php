<?php

namespace Devbanana\BudgetBundle\Controller;

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
     * @Template()
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

        $choices = array();
        foreach ($categories as $category)
        {
            $choices[$category->getId()] = "$category";
        }

        $form = $this->createFormBuilder()
            ->add('category', 'choice', array(
                        'choices' => $choices,
                        'empty_value' => ''
                        ))
            ->getForm();

        return array(
                'form' => $form->createView(),
            );    }

}
