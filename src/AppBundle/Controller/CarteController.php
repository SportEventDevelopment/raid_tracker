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
            'trace' => null,
            'token' => $this->getUser()->getToken()
        ));
        return new Response($content);
    }

    /**
     * @Route("/carte/edit/{id_parcours}", name="carte_edit")
     */
    public function editCarte(Request $request)
    {
        $user = $this->getUser();
        $url = 'api/traces/parcours/'.$request->get('id_parcours');
        $traces = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $content = $this->get('templating')->render('default/map_interactive.html.twig',array(
            'user' => $user,
            'trace' => $traces,
            'token' => $this->getUser()->getToken()
        ));
        return new Response($content);
    }

    /**
     * @Route("/carte/export/{id_parcours}", name="carte_export")
     */
    public function exportCarte(Request $request)
    {
        $user = $this->getUser();
        $url = 'api/traces/parcours/'.$request->get('id_parcours');
        $traces = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $content = $this->get('templating')->render('default/map_interactive.html.twig',array(
            'user' => $user,
            'trace' => $traces,
            'token' => $this->getUser()->getToken()
        ));
        return new Response($content);
    }
}