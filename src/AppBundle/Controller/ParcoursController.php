<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parcours;
use AppBundle\Entity\Raid;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Parcour controller.
 *
 * @Route("parcours")
 */
class ParcoursController extends Controller
{
    /**
     * Lists all parcour entities.
     *
     * @Route("/parcours", name="parcours")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $parcours = $em->getRepository('AppBundle:Parcours')->findAll();

        return $this->render('parcours/index.html.twig', array(
            'parcours' => $parcours,
            'user' =>$this->getUser()

        ));
    }

    /**
     * Creates a new parcour entity.
     *
     * @Route("/parcours/{id}/new", name="create_parcours")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $id)
    {
        $parcour = new Parcours();
        $form = $this->createForm('AppBundle\Form\ParcoursType', $parcour);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $parcour->setIdRaid($id);
            $em = $this->getDoctrine()->getManager();
            $em->persist($parcour);
            $em->flush();
            return $this->redirectToRoute('landing');

          //  return $this->redirectToRoute('parcours_show', array('id' => $parcour->getId()));
        }

        return $this->render('parcours/new.html.twig', array(
            'parcour' => $parcour,
            'form' => $form->createView(),
            'user' =>$this->getUser()
        ));
    }

    /**
     * Finds and displays a parcour entity.
     *
     * @Route("/{id}", name="parcours_show")
     * @Method("GET")
     */
    public function showAction(Parcours $parcour)
    {
        $deleteForm = $this->createDeleteForm($parcour);

        return $this->render('parcours/show.html.twig', array(
            'parcour' => $parcour,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing parcour entity.
     *
     * @Route("/{id}/edit", name="parcours_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Parcours $parcour)
    {
        $deleteForm = $this->createDeleteForm($parcour);
        $editForm = $this->createForm('AppBundle\Form\ParcoursType', $parcour);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('parcours_edit', array('id' => $parcour->getId()));
        }

        return $this->render('parcours/edit.html.twig', array(
            'parcour' => $parcour,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a parcour entity.
     *
     * @Route("/{id}", name="parcours_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Parcours $parcour)
    {
        $form = $this->createDeleteForm($parcour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($parcour);
            $em->flush();
        }

        return $this->redirectToRoute('parcours_index');
    }

    /**
     * Creates a form to delete a parcour entity.
     *
     * @param Parcours $parcour The parcour entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Parcours $parcour)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parcours_delete', array('id' => $parcour->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
