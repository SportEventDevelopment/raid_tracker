<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\RaidType;
use AppBundle\Entity\Raid;
use AppBundle\Entity\Organisateur;
use AppBundle\Entity\Parcours;
use \Unirest;

class RaidController extends Controller
{
    /**
     * @Route("/raids/create", name="create_raid")
     */
    public function createRaid(Request $request)
    {
        $raid = new Raid();
        $form = $this->createForm('AppBundle\Form\RaidType', $raid);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $raid_data =$this->get('app.serialize')->entityToArray($form->getData());
            $date = $form->getData()->getDate()->format('Y/m/d H:i');
            $raid_data['date'] = $date;

            $response = $this->get('app.restclient')->post(
                'api/raids',
                $raid_data,
                $this->getUser()->getToken()
            );
            
            $organisateur_data = array(
                'idUser' => $this->getUser()->getIdUser(),
                'idRaid' => $response->body->id
            );
            $response = $this->get('app.restclient')->post(
                'api/organisateurs/raids/'. $organisateur_data['idRaid'].'/users/'. $organisateur_data['idUser'], 
                $organisateur_data,
                $this->getUser()->getToken()
            );

            return $this->redirectToRoute('landing');
        }

        return $this->render('raid/new.html.twig', array(
            'user' => $this->getUser(),
            'raid' => $form->createView(),
        ));
    }

    /**
     * @Route("/raids/{id}", name="edit_raid")
     */
    public function descriptionRaidOrganisateurAction(Request $request)
    {
        $url = 'api/raids/'.$request->get('id');
        $raid = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $url = 'api/parcours/raids/'.$request->get('id');
        $all_parcours = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

       return $this->render('raid/description_raid_organisateur.html.twig', array(
            'user' => $this->getUser(),
            'all_parcours' => $all_parcours,
            'raid' => $raid
       ));
    }
    
    /**
     * Displays a form to edit an existing machin entity.
     *
     * @Route("/{id}/edit", name="raid_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Raid $raid)
    {
        //$deleteForm = $this->createDeleteForm($raid);
        $editForm = $this->createForm('AppBundle\Form\RaidType', $raid);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //return $this->redirectToRoute('raid_edit', array('id' => $raid->getId()));
            return $this->redirectToRoute('gestion_raid');
        }

        return $this->render('raid/edit.html.twig', array(
            'edit_form' => $editForm->createView(),
            'raid' => $raid,
            'user'=>$this->getUser()
            //'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/raids/{id}/gestion_parcours", name="gestion_parcours")
     */
    public function GestionRaidParcoursAction(Request $request,$id)
    {
        $raid =  $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Raid')
            ->findOneBy(array(
                'id' => $request->get('id')
            ));

        $all_parcours = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Raid')
                ->findAllParcoursByIdRaid($request->get('id'));

       return $this->render('raid/GestionParcoursRaid.html.twig', array(
           'user' => $this->getUser(),
          'all_parcours' => $all_parcours,
          'raid' => $raid
       ));
    }
}
