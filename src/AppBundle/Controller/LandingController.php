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

    $nomUserConnecte = $this->getUser()->getName();

    $em = $this->getDoctrine()->getManager();
    $tabRaidUsersConnecte = $em->getRepository('AppBundle:Organisateur')->findAllRaidOrga($this->getUser());

    $tab=[];

    foreach ($tabRaidUsersConnecte as $key => $valuetab) {
        foreach ($valuetab as $key => $value) {
            array_push($tab,$value);
          } }

    $tousRaidUsersConnecte=[];
    foreach ($tab as $key => $value) {
    array_push($tousRaidUsersConnecte,$em->getRepository('AppBundle:Raid')->find($value));
    }



    $tousRaidBenevoleUsersConnecte = $em->getRepository('AppBundle:Benevole')->findAllRaidBene($this->getUser());

    $tab=[];

    foreach ($tousRaidBenevoleUsersConnecte as $key => $valuetab) {
        foreach ($valuetab as $key => $value) {
            array_push($tab,$value);
          } }

    $tousRaidBenevoleUsersConnecte=[];
    foreach ($tab as $key => $value) {
    array_push($tousRaidBenevoleUsersConnecte,$em->getRepository('AppBundle:Raid')->find($value));
    }


    $tousLesRaids=$em->getRepository('AppBundle:Raid')->findAll();

    return $this->render('landing/index.html.twig', array(
      'user_name' => $nomUserConnecte,
      'tous_raids_connecte' => $tousRaidUsersConnecte,
      'tous_raids_benevole' => $tousRaidBenevoleUsersConnecte,

      'tous_raids' => $tousLesRaids,

  ));


   }


}
