<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Benevole;

class BenevoleController extends Controller
{
    /**
     * @Route("/api/benevoles", name="benevoles")
     * @Method({"GET"})
     */
    public function getBenevolesAction(Request $request)
    {
        $benevoles = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->findAll();
        
        /* @var $benevoles benevoles[] */
        $formatted = [];
        foreach ($benevoles as $benevole) {
            $formatted[] = [
                'idBenevole' => $benevole->getIdBenevole(),
                'idUser' => $benevole->getIdUser(),
                'idRaid' => $benevole->getIdRaid()    
            ];
        }

        return new JsonResponse($formatted);
    }

    /**
     * @Route("/api/benevoles/{id_benevole}", name="benevoles_one")
     * @Method({"GET"})
     */
    public function getBenevoleAction(Request $request)
    {
        $Benevole = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->find($request->get('id_benevole'));
        /* @var $Benevole Benevole */

        $formatted = [
            'idBenevole' => $Benevole->getIdBenevole(),
            'idUser' => $Benevole->getIdUser(),
            'idRaid' => $Benevole->getIdRaid()
        ];
        return new JsonResponse($formatted);
    }
}