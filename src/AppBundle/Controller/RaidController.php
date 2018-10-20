<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Raid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Organisateur;
use AppBundle\Entity\Benevole;

//use AppBundle\Form\BenevoleType;

/**
 * Raid controller.
 *
 * @Route("raid")
 */
class RaidController extends Controller
{
    /**
     * Lists all raid entities.
     *
     * @Route("/", name="raid_index")
     * @Method("GET")
     */
    public function indexAction()
    {
      //var_dump($this->getUser()->getEmail());die();
      /*
      $em = $this->getDoctrine()->getManager();
      $entities = $em->getRepository('AppBundle:Raid')->getTsRaid();
      var_dump($entities);die();*/
        $em = $this->getDoctrine()->getManager();
        $raids = $em->getRepository('AppBundle:Raid')->findAll();
        return $this->render('raid/index.html.twig', array(
            'raids' => $raids,
        ));
    }

    /**
     * Creates a new raid entity.
     *
     * @Route("/new", name="raid_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $raid = new Raid();
        $orga = new Organisateur();
        //var_dump($this->getUser());die();
        $form = $this->createForm('AppBundle\Form\RaidType', $raid);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //$ee=$raid->getTypeSport();
        //  var_dump($raid);die();
            $em->persist($raid);
            $em->flush();
            $orga->setIdUser($this->getUser()->getId());

            $orga->setIdRaid($raid->getId());

            $em->persist($orga);

            $em->flush();
         return $this->redirectToRoute('landing');
        }
        return $this->render('raid/new.html.twig', array(
            'raid' => $raid,
            'form' => $form->createView(),
        ));
    }
    /**
     * Finds and displays a raid entity.
     *
     * @Route("/{id}", name="raid_show")
     * @Method("GET")
     */
    public function showAction(Raid $raid)
    {
        $deleteForm = $this->createDeleteForm($raid);
        return $this->render('raid/show.html.twig', array(
            'raid' => $raid,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing raid entity.
     *
     * @Route("/{id}/edit", name="raid_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Raid $raid)
    {
      //  $deleteForm = $this->createDeleteForm($raid);

      $em = $this->getDoctrine()->getManager();

      $bene = new Benevole();

        $editForm = $this->createForm('AppBundle\Form\BenevoleType',$bene);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $bene->setIdUser($this->getUser()->getId());
            $bene->setIdRaid($raid->getId());

            $em->persist($bene);
            $em->flush();
            return $this->redirectToRoute('landing');
        }

        return $this->render('landing/benevoleAdd.html.twig', array(
            'raid' => $raid,
            'edit_form' => $editForm->createView(),
        //    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a raid entity.
     *
     * @Route("/{id}", name="raid_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Raid $raid)
    {
        $form = $this->createDeleteForm($raid);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($raid);
            $em->flush();
        }

        return $this->redirectToRoute('raid_index');
    }

    /**
     * Creates a form to delete a raid entity.
     *
     * @param Raid $raid The raid entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Raid $raid)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('raid_delete', array('id' => $raid->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
