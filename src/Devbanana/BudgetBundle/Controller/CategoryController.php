<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $masterCategories = $em->getRepository(
                'DevbananaBudgetBundle:MasterCategory')
            ->findOrderedMasterCategories($this->getUser());

        return array(
                'entities' => $masterCategories,
            );    }

        /**
         * @Route("/{id}",
         *     name="categories_show")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
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
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
         */
        public function newAjaxAction(MasterCategory $masterCategory = null)
        {
            if ($masterCategory) {
            $this->authorizeAccess($masterCategory);
            }

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
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
         */
        public function createAjaxAction(Request $request)
        {
            $category = new Category;
            $form = $this->createForm(new CategoryType(), $category);
            $form->handleRequest($request);

            if ($category->getMasterCategory()) {
                $this->authorizeAccess($category->getMasterCategory());
            }

                $response = new Response;
                $response->headers->set('Content-Type', 'application/json');

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

        /**
         * @Route("/reorder/up/{id}",
         *     name="categories_reorder_up",
         *     options={"expose":true})
         */
        public function reorderUpAction(Category $category)
        {
            $this->authorizeAccess($category->getMasterCategory());

            $content = array();
            $em = $this->getDoctrine()->getManager();

            $content['success'] = $em->getRepository('DevbananaBudgetBundle:Category')
                ->reorderUp($category);
            $em->flush();

            $response = new Response;
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($content));

            return $response;
        }

        /**
         * @Route("/reorder/down/{id}",
         *     name="categories_reorder_down",
         *     options={"expose":true})
         */
        public function reorderDownAction(Category $category)
        {
            $this->authorizeAccess($category->getMasterCategory());

            $content = array();
            $em = $this->getDoctrine()->getManager();

            $content['success'] = $em->getRepository('DevbananaBudgetBundle:Category')
                ->reorderDown($category);
            $em->flush();

            $response = new Response;
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($content));

            return $response;
        }

        /**
         * @Route("/{id}/delete",
         *     name="categories_delete",
         *     options={"expose":true})
         */
        public function deleteAjaxAction(Category $category)
        {
            $this->authorizeAccess($category->getMasterCategory());

            $em = $this->getDoctrine()->getManager();
$content = array();

if (!$em->getRepository('DevbananaBudgetBundle:LineItem')
        ->hasCategory($category)) {
    $em->remove($category);
    $em->flush();
$content['success'] = true;
}
else {
    $content['success'] = false;
    $content['error'] = 'You cannot delete a category with transactions. ' .
        'Please recategorize the transactions first.';
}

$response = new Response;
$response->headers->set('Content-Type', 'application/json');
$response->setContent(json_encode($content));

return $response;
        }

private function authorizeAccess(MasterCategory $masterCategory)
{
    if ($masterCategory->getUser() != $this->getUser()
            && !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
        $this->createAccessDeniedException();
    }
}

}
