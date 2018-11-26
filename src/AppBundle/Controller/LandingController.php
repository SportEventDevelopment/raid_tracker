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

    $user = $this->getUser();
    $raids_organisateurs = $this->get('doctrine.orm.entity_manager')
                            ->getRepository('AppBundle:Raid')
                            ->findRaidsOrganisateursByIdUser($this->getUser()->getId());

    $raids_benevoles = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Raid')
                        ->findRaidsBenevolesByIdUser($this->getUser()->getId());


    $raids_benevoles_choisis = $this->get('doctrine.orm.entity_manager')
                                            ->getRepository('AppBundle:Raid')
                                            ->findRaidsBenevolesChoisisByIdUser($this->getUser()->getId());



    $all_raids = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Raid')
                    ->findAllRaids();

    return $this->render('landing/index.html.twig', array(
      'user' => $user,
      'raids_organisateurs' => $raids_organisateurs,
      'raids_benevoles' => $raids_benevoles,
      'all_raids' => $all_raids,
      'raids_benevoles_choisis' => $raids_benevoles_choisis
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

     /**
      * @Route("/admin_benevole", name="admin_benevole")
      */
      public function AdminBenevoleAction(Request $request){

       $user = $this->getUser();
       $raids_organisateurs = $this->get('doctrine.orm.entity_manager')
                               ->getRepository('AppBundle:Raid')
                               ->findRaidsOrganisateursByIdUser($this->getUser()->getId());
      //                         var_dump($raids_organisateurs);die();

 //print_r($raids_organisateurs);die();
       return $this->render('landing/adminBenevole.html.twig', array(
         'user' => $user,
         'raids_organisateurs' => $raids_organisateurs,
      ));
      }
}
