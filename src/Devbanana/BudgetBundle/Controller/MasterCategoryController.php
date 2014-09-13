<?php

namespace Devbanana\BudgetBundle\Controller;

use Devbanana\BudgetBundle\Entity\MasterCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/master-categories")
 */
class MasterCategoryController extends Controller
{
    /**
     * @Route("/{id}", name="master_categories_show")
     * @Template()
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function showAction(MasterCategory $masterCategory)
    {
        return array(
            );    }

}
