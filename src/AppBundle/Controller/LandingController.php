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
    ->get('api/raids/visible/users/'.$this->getUser()->getIdUser(), $this->getUser()->getToken());

    return $this->render('landing/index.html.twig', array(
      'user' => $this->getUser(),
      'raids_organisateurs' => $raids_organisateurs,
      'raids_benevoles' => $raids_benevoles,
      'all_raids' => $all_raids
    ));
   }

  /**
    * @Route("/invitations", name="landing_invitation")
    */
    public function inviterBenevoles(Request $request){

      $user = $this->getUser();
      $url = 'api/raids/organisateurs/users/'.$this->getUser()->getIdUser();
      $raids_organisateurs = $this->get('app.restclient')
        ->get($url, $this->getUser()->getToken());

      return $this->render('landing/inviter.html.twig', array(
        'user' => $this->getUser(),
        'raids_organisateurs' => $raids_organisateurs
      ));
     }

    /**
      * @Route("/gerer-raids", name="landing_gerer_raid")
      */
      public function gererRaids(Request $request){

        $url = 'api/raids/organisateurs/users/'.$this->getUser()->getIdUser();
        $raids_organisateurs = $this->get('app.restclient')
          ->get($url, $this->getUser()->getToken());
          
        return $this->render('landing/gestionRaid.html.twig', array(
          'user' => $this->getUser(),
          'raids_organisateurs' => $raids_organisateurs,
        ));
      }

     /**
      * @Route("/admin_benevole", name="admin_benevole")
      */
      public function AdminBenevoleAction(Request $request){

       $user = $this->getUser();

       //var_dump($user->getUsername());die();
       $url = 'api/raids/organisateurs/users/'.$this->getUser()->getIdUser();
       $raids_organisateurs = $this->get('app.restclient')
                             ->get($url, $this->getUser()->getToken());
       return $this->render('landing/adminBenevole.html.twig', array(
         'user' => $this->getUser(),
         'raids_organisateurs' => $raids_organisateurs,
      ));
      }

    /**
      * @Route("/rejoindre-raid-benevole", name="raid_benevole_join")
      */
      public function raidBenevoleJoin(Request $request){
        $url = 'api/raids/benevoles/users/'.$this->getUser()->getIdUser();

        $raids_benevoles = $this->get('app.restclient')
          ->get($url, $this->getUser()->getToken());

        return $this->render('landing/gestionRaid.html.twig', array(
          'user' => $this->getUser(),
          'raids_benevoles' => $raids_benevoles,
        ));
      }
}
