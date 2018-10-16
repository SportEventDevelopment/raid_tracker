<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AdvertController extends Controller
{
  /**
   * @Route("/carte", name="carte")
   */
    public function indexAction()
    {
        $content = $this->get('templating')->render('default/map_interactive.html.twig',array('nom'=> "Maxime"));
        return new Response($content);
        //return $this->render('SEDInteractiveMapBundle:Default::index.html.twig');
    }
}
