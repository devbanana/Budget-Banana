<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/payees")
 */
class PayeeController extends Controller
{
    /**
     * @Route("/list/ajax", name="payees_list_ajax",
     * options={"expose":true})
     * @Method("POST")
     */
    public function listAjaxAction()
    {
        $em = $this->getDoctrine()->getManager();

        $payees = $em->getRepository('DevbananaBudgetBundle:Payee')
            ->findAllOrderedByName();

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
        $response->headers->set('Content-Type', 'Application/JSON');
        $response->setContent(json_encode($content));

        return $response;
           }

}
