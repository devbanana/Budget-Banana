<?php

namespace Devbanana\BudgetBundle\Controller;

use Devbanana\BudgetBundle\Entity\Payer;
use Devbanana\BudgetBundle\Form\PayerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/payers")
 */
class PayerController extends Controller
{
    /**
     * @Route("/list/ajax", name="payers_list_ajax", options={"expose":true})
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function listAjaxAction()
    {
        $em = $this->getDoctrine()->getManager();

$payers = $em->getRepository('DevbananaBudgetBundle:Payer')
    ->findAllOrderedByName($this->getUser());

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
$response->headers->set('Content-Type', 'application/json');
$response->setContent(json_encode($content));

return $response;
            }

    /**
     * @Route("/new/ajax", name="payers_new_ajax", options={"expose":true})
     * @Method("POST")
     * @Template()
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function newAjaxAction()
    {
        $payer = new Payer;
        $form = $this->createForm(new PayerType(), $payer);

        return array(
                'form' => $form->createView(),
                );
    }

    /**
     * @Route("/create/ajax", name="payers_create_ajax",
     * options={"expose":true})
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function createAjaxAction(Request $request)
    {
        $payer = new Payer;
        $payer->setUser($this->getUser());
        $form = $this->createForm(new PayerType(), $payer);
        $form->handleRequest($request);
        $content = array();

        if ($form->isValid()) {
            $content['success'] = true;

            $em = $this->getDoctrine()->getManager();
            $em->persist($payer);
            $em->flush();

            $content['id'] = $payer->getId();
            $content['name'] = $payer->getName();
        }

        $response = new Response;
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($content));

        return $response;
    }

}
