<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\User;

class AdvertController extends Controller
{
  /**
   * @Route("/carte", name="carte")
   */
    public function indexAction()
    {

    /*  $em = $this->getDoctrine()->getManager();

              $entities = $em->getRepository('AppBundle:User')->getTsUsers();

              var_dump($entities);die();*/

        $content = $this->get('templating')->render('default/map_interactive.html.twig',array('nom'=> "Maxime"));
        return new Response($content);
        //return $this->render('SEDInteractiveMapBundle:Default::index.html.twig');
    }
}
