<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parcours;
use AppBundle\Entity\Raid;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ParcoursController extends Controller
{

    /**
     * Creates a new parcour entity.
     * @Route("/parcours/raids/{id}", name="create_parcours")
     */
    public function createParcours(Request $request)
    {
        $parcours = new Parcours();
        $form = $this->createForm('AppBundle\Form\ParcoursType', $parcours);

        $form->handleRequest($request);

        $raid = $this->get('app.restclient')->get(
            'api/raids/'. $request->get('id'),
            $this->getUser()->getToken()
        );

        if ($form->isSubmitted() && $form->isValid()) {

            $parcours_data = $this->get('app.serialize')->entityToArray($form->getData());
            $parcours_data['idRaid'] = $request->get('id');
            
            $parcours = $this->get('app.restclient')->post(
                'api/parcours',
                $parcours_data,
                $this->getUser()->getToken()
            );

            return $this->redirectToRoute('carte');
        }

        return $this->render('parcours/new.html.twig', array(
            'user' =>$this->getUser(),
            'parcours' => $parcours,
            'form' => $form->createView(),
            'raid' => $raid
        ));
    }

    /**
     * Finds and displays a parcour entity.
     *
     * @Route("/parcours/{id}", name="parcours_show")
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
     * @Route("/parcours/{id}/edit", name="edition_parcours")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Parcours $parcour)
    {
        $deleteForm = $this->createDeleteForm($parcour);
        $editForm = $this->createForm('AppBundle\Form\ParcoursType', $parcour);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('edit_parcours', array('id' => $parcour->getId()));
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
