<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Raid;

class RaidController extends Controller
{
    /**
     * @Route("/api/raids", name="raids")
     * @Method({"GET"})
     */
    public function getRaidsAction(Request $request)
    {
        $raids = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->findAll();   
        /* @var $raids Raids[] */
        
        $formatted = [];
        foreach ($raids as $raid) {
            $formatted[] = [
                'idRaid' => $raid->getIdRaid(),
                'nom' => $raid->getNom(),
                'date' => $raid->getDate(),
                'lieu' => $raid->getLieu(),
                'edition' => $raid->getEdition(),
                'equipeOrganisatrice' => $raid->getEquipeOrganisatrice()    
            ];
        }

        return new JsonResponse($formatted);
    }

    /**
     * @Route("/api/raids/{id_raid}", name="raids_one")
     * @Method({"GET"})
     */
    public function getRaidAction(Request $request)
    {
        $raid = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->find($request->get('id_raid'));
        /* @var $raid Raid */

        $formatted = [
            'idRaid' => $raid->getIdRaid(),
            'nom' => $raid->getNom(),
            'date' => $raid->getDate(),
            'lieu' => $raid->getLieu(),
            'edition' => $raid->getEdition(),
            'equipeOrganisatrice' => $raid->getEquipeorganisatrice()    
        ];

        return new JsonResponse($formatted);
    }
}