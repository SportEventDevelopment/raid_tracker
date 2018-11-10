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
   public function LandingAction(Request $request){
    
    var_dump($this->getUser());die;
    $raids_organisateurs = 
      $headers = array('Accept' => 'application/json');      
      $response = Unirest\Request::get(
        'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/raids/organisateurs/users/'.
        $request->get('id_user'), 
        $headers
      );

      $raids_benevoles = 
        $headers = array('Accept' => 'application/json');      
        $response = Unirest\Request::get(
          'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/raids/benevoles/users/'.
          $request->get('id_user'), 
          $headers
        );
    
      $all_raids = 
        $headers = array('Accept' => 'application/json');      
        $response = Unirest\Request::get(
          'http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/raids/', $headers
        );

    return $this->render('landing/index.html.twig', array(
      'user' => $this->getUser(),
      'raids_organisateurs' => $raids_organisateurs,
      'raids_benevoles' => $raids_benevoles,
      'all_raids' => $all_raids,
    ));
   }
}
