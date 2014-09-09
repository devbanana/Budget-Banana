<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devbanana\BudgetBundle\Entity\MasterCategory;

/**
 * @Route("/master-categories")
 */
class MasterCategoryController extends Controller
{
    /**
     * @Route("/{id}", name="master_categories_show")
     * @Template()
     */
    public function showAction(MasterCategory $masterCategory)
    {
        return array(
            );    }

}
