<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devbanana\BudgetBundle\Entity\Account;
use Devbanana\BudgetBundle\Form\AccountType;
use Devbanana\BudgetBundle\Entity\Transaction;
use Devbanana\BudgetBundle\Entity\LineItem;
use Devbanana\BudgetBundle\Entity\Category;

/**
 * Account controller.
 *
 * @Route("/accounts")
 */
class AccountController extends Controller
{

    // {{{ public function indexAction()

    /**
     * Lists all Account entities.
     *
     * @Route("/", name="accounts")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DevbananaBudgetBundle:Account')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    // }}}

    // {{{ public function createAction(Request)

    /**
     * Creates a new Account entity.
     *
     * @Route("/", name="accounts_create")
     * @Method("POST")
     * @Template("DevbananaBudgetBundle:Account:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Account();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->em = $em;
            $em->persist($entity);

        $startingBalance = $form->get('startingBalance')->getData();
        if (bccomp(floatval($startingBalance), '0.00', 2)) {
            $this->createTransaction($startingBalance, $entity);
        }

            $em->flush();

            return $this->redirect($this->generateUrl('accounts_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    // }}}

    private function createTransaction($startingBalance, $entity)
    {
        // Create a starting balance transaction
        $transaction = new Transaction;
        $transaction->setDate(new \DateTime());

        $lineItem = new LineItem;
        $lineItem->setAccount($entity);

        // Find budget for current month
        $budget = $this->em->getRepository('DevbananaBudgetBundle:Budget')
            ->findOneOrCreateByDate($transaction->getDate());

        // Asset or liability?
        if (bccomp($startingBalance, '0.00', 2) >= 0) {
        $lineItem->setType('income');
            $lineItem->setInflow($startingBalance);

            if ($entity->getBudgeted()) {
        $lineItem->setAssignedMonth($budget);
            }
        }
        else {
            $lineItem->setType('expense');
            $lineItem->setOutflow(bcmul($startingBalance, '-1.00', 2));

            if ($entity->getBudgeted()) {
            // Create debt category
            $category = new Category;
            $category->setName($entity->getName());
            $category->setCarryOver('category');

            // Search for Debt category
            $masterCategory = $this->em->getRepository('DevbananaBudgetBundle:MasterCategory')
                ->findOneByName('Debt');

            $category->setMasterCategory($masterCategory);

            // Set order
                $order = $this->em->getRepository('DevbananaBudgetBundle:Category')
                    ->getHighestOrderFor($category->getMasterCategory());
                $category->setOrder($order + 1);
                $this->em->persist($category);
                $this->em->flush();

                // Get the budget category
                $budgetCategories = $this->em->getRepository('DevbananaBudgetBundle:BudgetCategories')
                    ->findOneBy(array(
                                'budget' => $budget,
                                'category' => $category,
                                ));

                $lineItem->setCategory($budgetCategories);
            }
        }

        $lineItem->setMemo('Starting Balance');

        $transaction->addLineItem($lineItem);

        $this->em->persist($lineItem);
        $this->em->persist($transaction);
    }

    // {{{ public function createAjaxAction(Request $request)

    /**
     * @Route("/create/ajax", name="accounts_create_ajax",
     * options={"expose":true})
     * @Method("POST")
     */
public function createAjaxAction(Request $request)
{
    $response = new Response;
    $response->headers->set('Content-Type', 'Application/JSON');
    $content = array();
        $entity = new Account();
        $form = $this->createCreateForm($entity, false);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->em = $em;
            $em->persist($entity);

            $startingBalance = $form->get('startingBalance')->getData();
            $this->createTransaction($startingBalance, $entity);

            $em->flush();

$content['id'] = $entity->getId();
$content['account'] = $entity->getName();
$content['success'] = true;
        }
        else {
            $content['success'] = false;
            // Get the errors
            $content['errors'] = array();
            foreach ($form->getErrors() as $error)
            {
                $content['errors'][] = $error->getMessage();
            }
        }

$response->setContent(json_encode($content));

return $response;
}

    // }}}

// {{{ public function listAjaxAction()

/**
 * @Route("/list/ajax", name="accounts_list_ajax", options={"expose":true})
 * @Method("POST")
 */
public function listAjaxAction()
{
    $em = $this->getDoctrine()->getManager();
    $accounts = $em->getRepository('DevbananaBudgetBundle:Account')
        ->findAll();

        $accountsArray = array();
    foreach ($accounts as $account)
    {
        $accountsArray[] = array(
                'id' => $account->getId(),
                'name' => $account->getName(),
                'balance' => $account->getBalance(),
                );
    }

    $content = array();
    $content['accounts'] = $accountsArray;

    $response = new Response;
    $response->headers->set('Content-Type', 'Application/JSON');
    $response->setContent(json_encode($content));

    return $response;
}

// }}}

    // {{{ private function createCreateForm(Entity)

    /**
     * Creates a form to create a Account entity.
     *
     * @param Account $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Account $entity, $submit = true)
    {
        $form = $this->createForm(new AccountType(), $entity, array(
            'action' => $this->generateUrl('accounts_create'),
            'method' => 'POST',
        ));

        if ($submit) {
            $form->add('submit', 'submit', array('label' => 'Create'));
        }

        return $form;
    }

    // }}}

    // {{{ public function newAction()

    /**
     * Displays a form to create a new Account entity.
     *
     * @Route("/new", name="accounts_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Account();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    // }}}

    // {{{ public function newAjaxAction()

    /**
     * Displays a form to create a new Account entity.
     *
     * @Route("/new/ajax", name="accounts_new_ajax", options={"expose":true})
     * @Method("POST")
     * @Template()
     */
    public function newAjaxAction()
    {
        $entity = new Account();
        $form   = $this->createCreateForm($entity, false);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    // }}}

    // {{{ public function showAction(mixed)

    /**
     * Finds and displays a Account entity.
     *
     * @Route("/{id}", name="accounts_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DevbananaBudgetBundle:Account')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Account entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    // }}}

    // {{{ public function editAction(mixed)

    /**
     * Displays a form to edit an existing Account entity.
     *
     * @Route("/{id}/edit", name="accounts_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DevbananaBudgetBundle:Account')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Account entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    // }}}

    // {{{ private function createEditForm(Account)

    /**
    * Creates a form to edit a Account entity.
    *
    * @param Account $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Account $entity)
    {
        $form = $this->createForm(new AccountType(), $entity, array(
            'action' => $this->generateUrl('accounts_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    // }}}

    // {{{ public function updateAction(Request, mixed)

    /**
     * Edits an existing Account entity.
     *
     * @Route("/{id}", name="accounts_update")
     * @Method("PUT")
     * @Template("DevbananaBudgetBundle:Account:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DevbananaBudgetBundle:Account')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Account entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('accounts_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    // }}}

    // {{{ public function deleteAction(mixed)

    /**
     * Deletes a Account entity.
     *
     * @Route("/{id}/delete", name="accounts_delete")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction($id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DevbananaBudgetBundle:Account')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Account.');
            }

        $form = $this->createDeleteForm($id);

        return array(
                'entity' => $entity,
                'form' => $form->createView()
                );
    }

    // }}}

    // {{{ public function confirmDeleteAction(Request, mixed)

    /**
      
      /**
       * @Route("/{id}", name="accounts_delete_confirm")
       * @Method("DELETE")
       * */
    public function confirmDeleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DevbananaBudgetBundle:Account')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Account.');
            }

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('accounts'));
        }

        return array(
                'entity' => $entity,
                'form' => $form->createView()
                );
    }

    // }}}

    // {{{ private function createDeleteForm(mixed)

    /**
     * Creates a form to delete a Account entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('accounts_delete_confirm', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    // }}}

}
