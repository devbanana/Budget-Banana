<?php

namespace Devbanana\BudgetBundle\Controller;

use Devbanana\BudgetBundle\Entity\LineItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/lineitems")
 */
class LineItemController extends Controller
{
    /**
     * @Route("/{id}/delete",
     *     name="lineitems_delete")
     * @Template()
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function deleteAction(Request $request, LineItem $lineItem)
    {
        $this->authorizeAccess($lineItem);

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

    if ($lineItem->getType() == 'transfer') {
        // Loop through line items to find the other one
        foreach ($lineItem->getTransaction()->getLineItems() as $l)
        {
            if ($l->getType() == 'transfer'
                    && $l->getAccount() == $lineItem->getTransferAccount()) {
                // Found it! Delete it! Utterly destroy it!
                $em->remove($l);
            }
        }

        if (count($lineItem->getTransaction()->getLineItems()) == 2) {
            // We only have both sides of the transfer in this transaction,
            // so delete the transaction
            $em->remove($lineItem->getTransaction());
        }
    }
    elseif (count($lineItem->getTransaction()->getLineItems()) == 1) {
    $em->remove($lineItem->getTransaction());
}

$em->flush();

return $this->redirect($this->generateUrl('transactions_index'));
        }

        return array(
                'form' => $form->createView(),
                'entity' => $lineItem,
            );    }

        private function authorizeAccess(LineItem $lineItem)
{
    if ($lineItem->getTransaction()->getUser() != $this->getUser()
            && !$this->get('security.context').isGranted('ROLE_ADMIN')) {
        $this->createAccessDeniedException();
    }
}

}
