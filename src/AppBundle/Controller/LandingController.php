<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \Unirest;

class LandingController extends Controller
{

  /**
   * @Route("/landing", name="landing")
   */
   public function Landing(Request $request){

    $url = 'api/raids/organisateurs/users/'.$this->getUser()->getIdUser();
    $raids_organisateurs = $this->get('app.restclient')
    ->get($url, $this->getUser()->getToken());

    $url = 'api/raids/benevoles/users/'.$this->getUser()->getIdUser();
    $raids_benevoles = $this->get('app.restclient')
    ->get($url, $this->getUser()->getToken());

    $all_raids = $this->get('app.restclient')
    ->get('api/raids', $this->getUser()->getToken());


    return $this->render('landing/index.html.twig', array(
      'user' => $this->getUser(),
      'raids_organisateurs' => $raids_organisateurs,
      'raids_benevoles' => $raids_benevoles,
      'all_raids' => $all_raids,
    ));
   }

   
   /**
    * @Route("/inviter", name="inviter")
    */
    public function InviterNewBeneoleAction(Request $request){

      $user = $this->getUser();
      $raids_organisateurs = $this->get('doctrine.orm.entity_manager')
                              ->getRepository('AppBundle:Raid')
                              ->findRaidsOrganisateursByIdUser($this->getUser()->getId());
 
      return $this->render('landing/inviter.html.twig', array(
        'user' => $user,
        'raids_organisateurs' => $raids_organisateurs,
     ));
     }
 
 
     /**
      * @Route("/gestion_raid", name="gestion_raid")
      */
      public function GestionRaidsAction(Request $request){
 
       $user = $this->getUser();
       $raids_organisateurs = $this->get('doctrine.orm.entity_manager')
                               ->getRepository('AppBundle:Raid')
                               ->findRaidsOrganisateursByIdUser($this->getUser()->getId());
 
 //print_r($raids_organisateurs);die();
       return $this->render('landing/gestionRaid.html.twig', array(
         'user' => $user,
         'raids_organisateurs' => $raids_organisateurs,
      ));
      }
}
