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

            // ****************************
            // Create new Raid with the API
            // ****************************
            $headers = array('Accept' => 'application/json');
            $data = $request->request->get('appbundle_raid');
            //format date to datetime
            $data['date'] = $data['date']['year'].'-'.$data['date']['month'].'-'.$data['date']['day'];
            $data['date'] = new \DateTime($data['date']);
            $data['date'] = $data['date']->format('Y/m/d H:i');
            
            //remove the token
            array_pop($data);
            $body = Unirest\Request\Body::form($data);
            $response = Unirest\Request::post('http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/raids', $headers, $body);
            
            // *****************************
            // Register the new organisateur
            // *****************************
            $headers = array('Accept' => 'application/json');
            $data = array(
                'idUser' => $this->getUser()->getId(),
                'idRaid' => $response->body->id
            );
            
            $body = Unirest\Request\Body::form($data);
            $response = Unirest\Request::post('http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/organisateurs/raids/'. $data['idRaid'].'/users/'. $data['idUser'], $headers, $body);

            return $this->forward('AppBundle\Controller\LandingController::LandingAction');
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
