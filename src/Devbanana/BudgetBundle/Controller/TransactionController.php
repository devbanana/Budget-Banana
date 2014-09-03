<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/transactions")
 */
class TransactionController extends Controller
{
    /**
     * @Route("/", name="transactions_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('DevbananaBudgetBundle:Transaction')
            ->findAll();

        return array(
                'entities' => $entities,
            );    }

    /**
     * @Route("/new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        return array(
            );    }

    /**
     * @Route("/{id}", name="transactions_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        return array(
            );    }

    /**
     * @Route("/transactions/{id}/edit")
     * @Template()
     */
    public function editAction($id)
    {
        return array(
                // ...
            );    }

    /**
     * @Route("/transactions/{id}/delete")
     * @Template()
     */
    public function deleteAction($id)
    {
        return array(
                // ...
            );    }

}
