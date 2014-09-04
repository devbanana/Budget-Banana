<?php

namespace Devbanana\BudgetBundle\Controller;

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
     * @Route("/list/ajax", name="payers_get_list_ajax", options={"expose":true})
     * @Method("POST")
     * @Template()
     */
    public function listAjaxAction()
    {
        $form = $this->createFormBuilder()
            ->add('payers', 'entity', array(
                        'class' => 'DevbananaBudgetBundle:Payer',
                        'property' => 'name',
                        'empty_value' => '',
                        ))
            ->getForm()
            ;
        return array(
                'form' => $form->createView(),
            );    }

}
