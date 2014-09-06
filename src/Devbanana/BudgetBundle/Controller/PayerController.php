<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/payers")
 */
class PayerController extends Controller
{
    /**
     * @Route("/list/ajax", name="payers_list_ajax", options={"expose":true})
     * @Method("POST")
     */
    public function listAjaxAction()
    {
        $em = $this->getDoctrine()->getManager();

$payers = $em->getRepository('DevbananaBudgetBundle:Payer')
    ->findAllOrderedByName();

$content = array();
$content['payers'] = array();

foreach ($payers as $payer)
{
    $content['payers'][] = array(
            'id' => $payer->getId(),
            'name' => "$payer",
            );
}

$response = new Response;
$response->headers->set('Content-Type', 'Application/JSON');
$response->setContent(json_encode($content));

return $response;
            }

}
