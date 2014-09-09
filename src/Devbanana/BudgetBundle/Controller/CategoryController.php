<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devbanana\BudgetBundle\Entity\MasterCategory;
use Devbanana\BudgetBundle\Entity\Category;
use Devbanana\BudgetBundle\Form\CategoryType;

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

        /**
         * @Route("/new/ajax/{id}",
         *     name="categories_new_ajax",
         *     defaults={"id" = null},
         *     options={"expose":true})
         * @Template()
         */
        public function newAjaxAction(MasterCategory $masterCategory = null)
        {
            $category = new Category;
            $category->setMasterCategory($masterCategory);
            $form = $this->createForm(new CategoryType(), $category);

            return array(
                    'form' => $form->createView(),
                    );
        }

        /**
         * @Route("/create/ajax",
         *     name="categories_create_ajax",
         *     options={"expose":true})
         */
        public function createAjaxAction(Request $request)
        {
            $category = new Category;
            $form = $this->createForm(new CategoryType(), $category);
            $form->handleRequest($request);

                $response = new Response;
                $response->headers->set('Content-Type', 'Application/JSON');

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $order = $em->getRepository('DevbananaBudgetBundle:Category')
                    ->getHighestOrderFor($category->getMasterCategory());
                $category->setOrder($order + 1);

                $em->persist($category);
                $em->flush();

                $content = array();
                $content['id'] = $category->getId();
                $content['name'] = $category->getName();
                $content['url'] = $this->generateUrl('categories_show', array(
                            'id' => $category->getId(),
                            ));

                $response->setContent(json_encode($content));
            }
            else {
            $response->setContent(json_encode(array('success' => false)));
            }

            return $response;
        }

}
