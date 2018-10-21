<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Organisateur;

class OrganisateurController extends Controller
{

    /**
     * @Route("/api/organisateurs", name="get_all_organisateurs")
     * @Method({"GET"})
     */
    public function getOrganisateursAction(Request $request)
    {
        $organisateurs = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Organisateur')
                ->findAll();   
        /* @var $organisateurs Organisateurs[] */
        
        if (empty($organisateurs)) {
            return new JsonResponse(['message' => "Aucun organisateurs présents dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [];
        foreach ($organisateurs as $organisateur) {
            $formatted[] = [
                'id' => $organisateur->getId(),
                'idUser' => $organisateur->getIdUser(),
                'idRaid' => $organisateur->getIdRaid()   
            ];
        }

        return new JsonResponse($formatted, Response::HTTP_OK);
    }

    /**
     * @Route("/api/organisateurs", name="delete_all_organisateurs")
     * @Method({"DELETE"})
     */
    public function deleteOrganisateursAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $organisateurs = $em->getRepository('AppBundle:Organisateur')->findAll();

        foreach ($organisateurs as $organisateur) {
            $em->remove($organisateur);
        }

        $em->flush();

        return new JsonResponse(["message" => "Les organisateurs ont été supprimés avec succès !"], Response::HTTP_OK);
    }

    /**
     * @Route("/api/organisateurs/{id_organisateur}", name="get_organisateurs_one")
     * @Method({"GET"})
     */
    public function getOrganisateurAction(Request $request)
    {
        $organisateur = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Organisateur')
                ->find($request->get('id_organisateur'));
        /* @var $organisateur organisateur */

        if(empty($organisateur)){
            return new JsonResponse(["message" => "Organisateur non trouvé !", Response::HTTP_NOT_FOUND]);
        }

        $formatted = [
            'id' => $organisateur->getId(),
            'idUser' => $organisateur->getIdUser(),
            'idRaid' => $organisateur->getIdRaid()
        ];

        return new JsonResponse($formatted, Response::HTTP_OK);
    }


    /**
     * @Route("/api/organisateurs/{id_organisateur}", name="delete_organisateurs_one")
     * @Method({"DELETE"})
     */
    public function deleteOrganisateurAction(Request $request)
    {
        
        $sn = $this->getDoctrine()->getManager();
        $organisateur = $this->getDoctrine()->getRepository('AppBundle:Organisateur')->find($request->get('id_organisateur'));
       
        if (empty($organisateur)) {
            return new JsonResponse(["message" => "Organisateur non trouvé !"], Response::HTTP_NOT_FOUND);
        }

        $sn->remove($organisateur);
        $sn->flush();
        
        return new JsonResponse(["message" => "Organisateur supprime avec succès !"], Response::HTTP_OK); 
    }

   /**
     * @Route("/api/organisateurs/raids/{id_raid}", name="get_all_organisateurs_one_raid")
     * @Method({"GET"})
     */
    public function getOrganisateursByIdRaidAction(Request $request)
    {
        $organisateurs = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Organisateur')
                ->findBy(array("idRaid" => $request->get('id_raid')));   
        /* @var $organisateurs Organisateurs[] */
        
        if (empty($organisateurs)) {
            return new JsonResponse(["message" => "Aucun organisateur pour ce raid !"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [];
        foreach ($organisateurs as $organisateur) {
            $formatted[] = [
                'id' => $organisateur->getId(),
                'idUser' => $organisateur->getIdUser(),
                'idRaid' => $organisateur->getIdRaid()   
            ];
        }

        return new JsonResponse($formatted, Response::HTTP_OK);
    }

    /**
     * @Route("/api/organisateurs/raids/{id_raid}", name="delete_all_organisateurs_one_raid")
     * @Method({"DELETE"})
     */
    public function deleteOrganisateursByIdRaidAction(Request $request)
    {
        
        $sn = $this->getDoctrine()->getManager();
        $organisateurs = $this->getDoctrine()->getRepository('AppBundle:Organisateur')
                        ->findBy(array("idRaid" => $request->get('id_raid')));
       
        if (empty($organisateurs)) {
            return new JsonResponse(['message' => "Aucun organisateur dans ce raid !"], Response::HTTP_NOT_FOUND);
        }

        foreach ($organisateurs as $organisateur) {
            $sn->remove($organisateur);
        }
        $sn->flush();
        
        return new JsonResponse(["message" => "Organisateurs supprimes avec succès !"], Response::HTTP_OK); 
    }

    /**
     * @Route("/api/organisateurs/raids/{id_raid}/users/{id_user}", name="get_raid_if_user_is_organisateur")
     * @Method({"GET"})
     */
    public function getIsOrganisateurByUserId(Request $request)
    {
        $organisateur = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Organisateur')
                ->findOneBy(array(  
                    'idRaid' => $request->get('id_raid'), 
                    'idUser' => $request->get('id_user'))
                );

        if(empty($organisateur)){
            return new JsonResponse(["message" => "L'utilisateur n'appartient pas à l'équipe d'organisation"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [
            'id' => $organisateur->getId(),
            'idRaid' => $organisateur->getIdRaid(),
            'idUser' => $organisateur->getIdUser()
        ];

        return new JsonResponse($formatted,Response::HTTP_OK);
    }


    /**
     *  @Route("/api/organisateurs/raids/{id_raid}/users/{id_user}", name="post_organisateur_one_raid")
     *  @Method({"POST"})
     */
    public function postOrganisateurByIdRaidAndByIdUser(Request $request)
    {
        $organisateur = new Organisateur();

        $organisateur->setIdUser($request->get('id_user'));
        $organisateur->setIdRaid($request->get('id_raid'));

        if(empty($organisateur)){
            return new JsonResponse(["message" => "Champs vide, création refusée !"], Response::HTTP_NOT_FOUND);
        }
        // Save
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($organisateur);
        $em->flush();

        return new JsonResponse(["message" => "Nouvel organisateur ajouté !"], Response::HTTP_OK);
    }

    /**
     * @Route("/api/organisateurs/raids/{id_raid}/users/{id_user}", name="delete_organisateur_one_raid")
     * @Method({"DELETE"})
     */
    public function deleteOrganisateurByIdRaidAndByIdUser(Request $request)
    {
        
        $sn = $this->getDoctrine()->getManager();
        $organisateur = $this->getDoctrine()->getRepository('AppBundle:Organisateur')
                        ->findOneBy(array(
                            "idUser" => $request->get('id_user'),
                            "idRaid" => $request->get('id_raid')
                        ));
       
        if (empty($organisateur)) {
            return new JsonResponse(['message' => "L'organisateur non trouve dans ce raid !"], Response::HTTP_NOT_FOUND);
        }

        $sn->remove($organisateur);
        $sn->flush();
        
        return new JsonResponse(['message' => "Organisateur supprime avec succes !"], Response::HTTP_OK); 
    }
}
