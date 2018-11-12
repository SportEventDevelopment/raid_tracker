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
     *
     * @Route("/parcours/raids/{id}", name="create_parcours")
     */
    public function createParcours(Request $request)
    {
        $parcour = new Parcours();
        $form = $this->createForm('AppBundle\Form\ParcoursType', $parcour);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $raid = $this->get('app.restclient')->get(
                'api/raids/'. $request->get('id'),
                $this->getUser()->getToken()
            );

            $parcours_data = $this->get('app.serialize')->entityToArray($form->getData());
            $parcours_data['idRaid'] = $request->get('id');
            
            $parcours = $this->get('app.restclient')->post(
                'api/parcours',
                $parcours_data,
                $this->getUser()->getToken()
            );

            return $this->redirectToRoute('landing');
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
     * @Route("/parcours/{id}/edit", name="edit_parcours")
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
}
