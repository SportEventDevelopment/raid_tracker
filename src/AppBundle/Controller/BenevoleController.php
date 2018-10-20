<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        if (empty($benevoles)) {
            return new JsonResponse(['message' => "Aucun benevoles présents dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [];
        foreach ($benevoles as $benevole) {
            $formatted[] = [
                'id' => $benevole->getId(),
                'idUser' => $benevole->getIdUser(),
                'idRaid' => $benevole->getIdRaid()    
            ];
        }

        return new JsonResponse($formatted, Response::HTTP_OK);
    }

    /**
     * @Route("/api/benevoles", name="delete_all_benevoles")
     * @Method({"DELETE"})
     */
    public function deleteBenevolesAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $benevoles = $em->getRepository('AppBundle:Benevole')->findAll();

        foreach ($benevoles as $benevole) {
            $em->remove($benevole);
        }

        $em->flush();

        return new JsonResponse(["message" => "Les benevoles ont ete supprimes avec succes !"], Response::HTTP_OK);
    }

    /**
     * @Route("/api/benevoles/{id_benevole}", name="benevoles_one")
     * @Method({"GET"})
     */
    public function getBenevoleAction(Request $request)
    {
        $benevole = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->find($request->get('id_benevole'));
        /* @var $benevole Benevole */

        if (empty($benevole)) {
            return new JsonResponse(['message' => "Le bénévole recherché n'a pas été trouvé !"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [
            'id' => $benevole->getId(),
            'idUser' => $benevole->getIdUser(),
            'idRaid' => $benevole->getIdRaid()
        ];
        return new JsonResponse($formatted);
    }


    /**
     * @Route("/api/benevoles/{id_benevole}", name="delete_benevoles_one")
     * @Method({"DELETE"})
     */
    public function deleteBenevoleAction(Request $request)
    {
        $sn = $this->getDoctrine()->getManager();
        $benevole = $this->getDoctrine()->getRepository('AppBundle:Benevole')->find($request->get('id_benevole'));
       
        if (empty($benevole)) {
            return new JsonResponse(['message' => "Le bénévole recherché n'a pas été trouvé !"], Response::HTTP_NOT_FOUND);
        }

        $sn->remove($benevole);
        $sn->flush();
        
        return new JsonResponse(['message' => "Bénévole supprimé avec succès !"], Response::HTTP_OK); 
    }


    /**
     * @Route("/api/benevoles/raids/{id_raid}", name="get_all_benevoles_raid")
     * @Method({"GET"})
     */
    public function getBenevolesByIdRaidAction(Request $request)
    {
        $benevoles = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->findBy(array(
                    "idRaid" => $request->get('id_raid')));
        /* @var $benevole Benevole */

        if (empty($benevoles)) {
            return new JsonResponse(['message' => "Le raid ne contient pas encore de bénévoles !"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [];
        foreach ($benevoles as $benevole) {
            $formatted[] = [
                'id' => $benevole->getId(),
                'idUser' => $benevole->getIdUser(),
                'idRaid' => $benevole->getIdRaid()
            ];
        }
        
        return new JsonResponse($formatted, Response::HTTP_OK);
    }

    /**
     * @Route("/api/benevoles/raids/{id_raid}", name="delete_all_benevoles_raid")
     * @Method({"DELETE"})
     */
    public function deleteBenevolesByIdRaidAction(Request $request)
    {   
        $sn = $this->getDoctrine()->getManager();
        $benevoles = $this->getDoctrine()->getRepository('AppBundle:Benevole')
                    ->findBy(array(
                        "idRaid" => $request->get('id_raid')
                    ));
       
        if (empty($benevoles)) {
            return new JsonResponse(['message' => "Aucun bénévole à supprimer trouvé dans ce raid !"], Response::HTTP_NOT_FOUND);
        }

        foreach ($benevoles as $benevole) {
            $sn->remove($benevole);
        }
        $sn->flush();
        
        return new JsonResponse(['message' => "Tous les bénévoles du raid ont été supprimés avec succes !"], Response::HTTP_OK); 
    }




    /**
     * @Route("/api/benevoles/raids/{id_raid}/users/{id_user}", name="get_raid_if_user_is_benevole")
     * @Method({"GET"})
     */
    public function getIsOrganisateurByUserId(Request $request)
    {
        $benevole = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->findOneBy(array(  
                    'idRaid' => $request->get('id_raid'), 
                    'idUser' => $request->get('id_user'))
                );

        if(empty($benevole)){
            return new JsonResponse(["message" => "L'utilisateur n'est pas bénévole du raid"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [
            'id' => $benevole->getId(),
            'idRaid' => $benevole->getIdRaid(),
            'idUser' => $benevole->getIdUser()
        ];

        return new JsonResponse($formatted,Response::HTTP_OK);
    }


    /**
     *  @Route("/api/benevoles/raids/{id_raid}/users/{id_user}", name="post_benevole_one_raid")
     *  @Method({"POST"})
     */
    public function postBenevoleByIdRaidAndByIdUser(Request $request)
    {
        $benevole = new Benevole();

        $benevole->setIdUser($request->get('id_user'));
        $benevole->setIdRaid($request->get('id_raid'));

        if(empty($benevole)){
            return new JsonResponse(["message" => "Champs vide, création refusée !"], Response::HTTP_NOT_FOUND);
        }
        // Save
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($benevole);
        $em->flush();

        return new JsonResponse(['message' => 'Nouveau bénévole ajouté !'], Response::HTTP_OK);
    }

    /**
     * @Route("/api/benevoles/raids/{id_raid}/users/{id_user}", name="delete_benevole_one_raid")
     * @Method({"DELETE"})
     */
    public function deleteBenevoleByIdRaidAndByIdUser(Request $request)
    {
        
        $sn = $this->getDoctrine()->getManager();
        $benevole = $this->getDoctrine()->getRepository('AppBundle:Benevole')
                        ->findOneBy(array(
                            "idUser" => $request->get('id_user'),
                            "idRaid" => $request->get('id_raid')
                        ));
       
        if (empty($benevole)) {
            return new JsonResponse(['message' => "Le bénévole n'est pas dans ce raid !"], Response::HTTP_NOT_FOUND);
        }

        $sn->remove($benevole);
        $sn->flush();
        
        return new JsonResponse(['message' => "Bénévole supprimé du raid avec succes !"], Response::HTTP_OK); 
    }
}