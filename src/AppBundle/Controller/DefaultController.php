<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
  




    /**
      * @Route("/sucess", name="success")
      */
 public function ConnexionReussieAction(){
   return $this->render('default/index.html.twig',
    array('user' => $this->getUser()->getName(),
   )
   );
 }


}
