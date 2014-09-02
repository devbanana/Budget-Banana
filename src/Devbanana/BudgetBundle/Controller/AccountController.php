<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Devbanana\BudgetBundle\Entity\Account;
use Devbanana\BudgetBundle\Form\AccountType;

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
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('accounts_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
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
    private function createCreateForm(Account $entity)
    {
        $form = $this->createForm(new AccountType(), $entity, array(
            'action' => $this->generateUrl('accounts_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

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
