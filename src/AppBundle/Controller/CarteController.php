<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parcours;
use AppBundle\Entity\Raid;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CarteController extends Controller
{
    /**
     * @Route("/carte", name="carte")
     */
    public function carte()
    {
        $user = $this->getUser();
        $content = $this->get('templating')->render('default/map_interactive.html.twig',array(
            'user' => $user
        ));
        return new Response($content);
    }
}