<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Devbanana\BudgetBundle\Entity\Payee;
use Devbanana\BudgetBundle\Form\PayeeType;

/**
 * @Route("/payees")
 */
class PayeeController extends Controller
{
    /**
     * @Route("/list/ajax", name="payees_list_ajax",
     * options={"expose":true})
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function listAjaxAction()
    {
        $em = $this->getDoctrine()->getManager();

        $payees = $em->getRepository('DevbananaBudgetBundle:Payee')
            ->findAllOrderedByName($this->getUser());

        $content = array();
        $content['payees'] = array();

        foreach ($payees as $payee)
        {
            $content['payees'][] = array(
                    'id' => $payee->getId(),
                    'name' => "$payee",
                    );
        }

        $response = new Response;
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($content));

        return $response;
    }

    /**
     * @Route("/new/ajax", name="payees_new_ajax", options={"expose":true})
     * @Method("POST")
     * @Template()
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function newAjaxAction()
    {
        $payee = new Payee;
        $form = $this->createForm(new PayeeType(), $payee);

        return array(
                'form' => $form->createView(),
                );
    }

    /**
     * @Route("/create/ajax", name="payees_create_ajax",
     * options={"expose":true})
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function createAjaxAction(Request $request)
    {
        $payee = new Payee();
        $payee->setUser($this->getUser());
        $form = $this->createForm(new PayeeType(), $payee);
        $form->handleRequest($request);
        $content = array();

        if ($form->isValid()) {
            $content['success'] = true;

            $em = $this->getDoctrine()->getManager();
            $em->persist($payee);
            $em->flush();

            $content['id'] = $payee->getId();
            $content['name'] = $payee->getName();
        }
        else {
            $content['success'] = false;
            $content['errors'] = array();
            foreach ($form->getErrors() as $error)
            {
                $content['errors'][] = $error->getMessage();
            }
        }

        $response = new Response;
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($content));

        return $response;
    }

}
