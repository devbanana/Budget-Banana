<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devbanana\BudgetBundle\Entity\Category;

/**
 * @Route("/categories")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/", name="categories_index")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $masterCategories = $em->getRepository(
                'DevbananaBudgetBundle:MasterCategory')
            ->findOrderedMasterCategories();

        return array(
                'entities' => $masterCategories,
            );    }

        /**
         * @Route("/{id}",
         *     name="categories_show")
         */
        public function showAction(Category $category)
        {
        }

}
