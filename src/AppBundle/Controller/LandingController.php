<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Raid;
use AppBundle\Entity\Organisateur;

class LandingController extends Controller
{

  /**
   * @Route("/landing", name="landing")
   */
   public function LandingAction(Request $request){
    
    $user_name = $this->getUser()->getName();
    $raids_organisateurs = $this->get('doctrine.orm.entity_manager')
                            ->getRepository('AppBundle:Raid')
                            ->findRaidsOrganisateursByIdUser($this->getUser()->getId());

    $raids_benevoles = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Raid')
                        ->findRaidsBenevolesByIdUser($this->getUser()->getId());
    
    $all_raids = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Raid')
                    ->findAllRaids();

    return $this->render('landing/index.html.twig', array(
      'user_name' => $user_name,
      'raids_organisateurs' => $raids_organisateurs,
      'raids_benevoles' => $raids_benevoles,
      'all_raids' => $all_raids,
    ));
   }


}
