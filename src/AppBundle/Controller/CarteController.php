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
        $content = $this->get('templating')->render('default/map_interactive.html.twig',array(
            'user' => $this->getUser(),
            'token' => $this->getUser()->getToken()
        ));
        return new Response($content);
    }

    /**
     * @Route("/carte/edit/{id_parcours}", name="carte_edit")
     */
    public function editCarte(Request $request)
    {
        $content = $this->get('templating')->render('default/map_interactive.html.twig',array(
            'user' => $this->getUser(),
            'idparcours' => $request->get('id_parcours'),
            'token' => $this->getUser()->getToken()
        ));
        return new Response($content);
    }
}