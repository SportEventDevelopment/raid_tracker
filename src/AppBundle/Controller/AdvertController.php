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
        $user = $this->getUser();
        $content = $this->get('templating')->render('default/map_interactive.html.twig',array('user' => $user));
        return new Response($content);
        //return $this->render('SEDInteractiveMapBundle:Default::index.html.twig');
    }
}
