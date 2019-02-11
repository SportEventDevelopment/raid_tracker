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
     * @Route("/carte/edit/raid/{id_raid}/parcours/{id_parcours}", name="carte_edit")
     */
    public function editCarte(Request $request)
    {

        $est_organisateur = null;
        $url = 'api/raids/'.$request->get('id_raid');
        $raid = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        if($raid){
            $url = 'api/organisateurs/raids/'. $request->get('id_raid') .'/users/'. $this->getUser()->getIdUser();
            $est_organisateur = $this->get('app.restclient')
                ->get($url, $this->getUser()->getToken());
        }
        
        $content = $this->get('templating')->render('default/map_interactive.html.twig',array(
            'user' => $this->getUser(),
            'est_organisateur' => $est_organisateur,
            'idparcours' => $request->get('id_parcours'),
            'token' => $this->getUser()->getToken(),
            'raid' => $raid
        ));
        return new Response($content);
    }
}
