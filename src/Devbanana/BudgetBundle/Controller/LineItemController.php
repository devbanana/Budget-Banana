<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devbanana\BudgetBundle\Entity\LineItem;

/**
 * @Route("/lineitems")
 */
class LineItemController extends Controller
{
    /**
     * @Route("/{id}/delete",
     *     name="lineitems_delete")
     * @Template()
     */
    public function deleteAction(Request $request, LineItem $lineItem)
    {
        $form = $this->createFormBuilder()
            ->setMethod('delete')
            ->add('submit', 'submit', array(
                        'label' => 'Delete',
                        ))
            ->getForm()
            ;
        $form->handleRequest($request);

        if ($form->isValid()) {
$em = $this->getDoctrine()->getManager();
$em->remove($lineItem);

if (count($lineItem->getTransaction()) == 1) {
    $em->remove($lineItem->getTransaction());
}
$em->flush();

return $this->redirect($this->generateUrl('transactions_index'));
        }

        return array(
                'form' => $form->createView(),
                'entity' => $lineItem,
            );    }

}
