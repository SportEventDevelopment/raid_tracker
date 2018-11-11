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
     * @Route("/raids/{id}/description_raid_organisateur", name="description_organisateur_raid")
     */
    public function descriptionRaidOrganisateurAction(Request $request,$id)
    {
        $raid =  $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Raid')
            ->findOneBy(array(
                'id' => $request->get('id')
            ));

        $all_parcours = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Raid')
                ->findAllParcoursByIdRaid($request->get('id'));

       return $this->render('raid/description_raid_organisateur.html.twig', array(
            'user' => $this->getUser(),
            'all_parcours' => $all_parcours,
            'raid' => $raid
       ));
    }
}
