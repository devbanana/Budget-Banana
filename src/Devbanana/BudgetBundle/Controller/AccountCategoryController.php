<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devbanana\BudgetBundle\Entity\AccountCategory;

/**
 * @Route("/account-categories")
 */
class AccountCategoryController extends Controller
{

    /**
     * @Route("/get/budgeted/ajax/{id}",
     *     name="accountcategories_get_budgeted_ajax",
     *     options={"expose":true})
     */
    public function getBudgetedAjaxAction(AccountCategory $accountCategory)
    {
        $content = array();
        $content['budgeted'] = intval($accountCategory->getBudgeted());

        $response = new Response;
        $response->headers->set('Content-Type', 'Application/JSON');
        $response->setContent(json_encode($content));

        return $response;
    }

}
