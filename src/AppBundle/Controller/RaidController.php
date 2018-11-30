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

/**
 * Raid controller.
 */
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
     * @Route("/raids/{id}/user/{id2}/choix_benevole_orga", name="choix_benevole_orga")
     */
    public function choixBenevoleAction(Request $request,$id,$id2)
    {
        $raid =  $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Raid')
            ->findOneBy(array(
                'id' => $request->get('id')
            ));

        $all_postes = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Raid')
                ->findPosteByIdRaid($request->get('id'));
        $em = $this->getDoctrine()->getManager();

       return $this->render('raid/choixBenevole.html.twig', array(
            'user' => $this->getUser(),
            'all_postes' => $all_postes,
            'raid' => $raid
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

    /**
     * @Route("/raids/{id_raid}/description", name="raid_description_edit")
     */
    public function editAction(Request $request)
    {
        $url = 'api/raids/'.$request->get('id_raid');
        $raid_data = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $raid = new Raid();
        $raid->setEquipe($raid_data->equipe);
        $raid->setDate(new \DateTime($raid_data->date));
        $raid->setNom($raid_data->nom);
        $raid->setLieu($raid_data->lieu);
        $raid->setEdition($raid_data->edition);
        $raid->setVisibility($raid_data->visibility);

        $editForm = $this->createForm('AppBundle\Form\RaidType', $raid);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            
            $url = 'api/raids/'.$request->get('id_raid');
            $t_raid =$this->get('app.serialize')->entityToArray($raid);
            $date = $editForm->getData()->getDate()->format('Y/m/d H:i');
            $t_raid['date'] = $date;

            if ($t_raid['visibility'] == false){
                unset($t_raid['visibility']);
            }
            $edit_raid = $this->get('app.restclient')->post($url, $t_raid, $this->getUser()->getToken());
            
            return $this->redirectToRoute('landing_gerer_raid');
        }

        return $this->render('raid/edit.html.twig', array(
            'user'=>$this->getUser(),
            'edit_form' => $editForm->createView()
        ));
    }
}
