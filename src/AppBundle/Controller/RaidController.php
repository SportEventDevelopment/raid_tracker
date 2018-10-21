<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\RaidType;
use AppBundle\Entity\Raid;
use AppBundle\Entity\Organisateur;

class RaidController extends Controller
{
    /**
     * @Route("/raids/create", name="create_raid")
     */
    public function createRaid(Request $request) 
    {
        $raid = new Raid();
        $orga = new Organisateur();
        
        $form = $this->createForm('AppBundle\Form\RaidType', $raid);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //$ee=$raid->getTypeSport();
            //  var_dump($raid);die();
            $em->persist($raid);
            $em->flush();
            $orga->setIdUser($this->getUser()->getId());
            $orga->setIdRaid($raid->getId());
            $em->persist($orga);
            $em->flush();
            return $this->redirectToRoute('landing');
        }

        return $this->render('raid/new.html.twig', array(
            'user' => $this->getUser(),
            'raid' => $form->createView(),
        ));
    }

    /**
     * @Route("/raids/description_raid_organisateur", name="description_organisateur_raid")
     */
    public function descriptionRaidOrganisateurAction(Request $request) 
    {


        return $this->render('raid/description_raid_organisateur.html.twig', array(
            'user' => $this->getUser()
        ));
    }

    /**
     * @Route("/api/raids", name="get_all_raids")
     * @Method({"GET"})
     */
    public function getRaidsAction(Request $request)
    {
        $raids = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->findAll();   
        /* @var $raids Raids[] */
        
        if(empty($raids)){
            return new JsonResponse(["message" => "Aucun RAID trouvé dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [];
        foreach ($raids as $raid) {
            $formatted[] = [
                'id' => $raid->getId(),
                'nom' => $raid->getNom(),
                'lieu' => $raid->getLieu(),
                'date' => $raid->getDate(),
                'edition' => $raid->getEdition(),
                'equipe' => $raid->getEquipe()    
            ];
        }

        return new JsonResponse($formatted, Response::HTTP_OK);
    }

    /**
     * @Route("/api/raids", name="post_all_raids")
     * @Method({"POST"})
     */
    public function postRaidsAction(Request $request)
    {
        $raid = new Raid();
        
        $raid->setNom($request->get('nom'));
        $raid->setLieu($request->get('lieu'));
        $date = new \DateTime($request->get('date'));
        $raid->setDate($date);
        $raid->setEdition($request->get('edition'));
        $raid->setEquipe($request->get('equipe'));

        // Save
        $em = $this->getDoctrine()->getManager();
        $em->persist($raid);
        $em->flush();

        return new JsonResponse(['message' => 'Raid ajouté !'], Response::HTTP_OK);
    }


    /**
     * @Route("/api/raids", name="delete_all_raids")
     * @Method({"DELETE"})
     */
    public function deleteRaidsAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $raids = $em->getRepository('AppBundle:Raid')->findAll();

        if(empty($raids)){
            return new JsonResponse(["message" => "Aucun RAID présents dans la BDD !"], Response::HTTP_NOT_FOUND);    
        }

        foreach ($raids as $raid) {
            $em->remove($raid);
        }

        $em->flush();

        return new JsonResponse(["message" => "Les raids ont été supprimés avec succès !"], Response::HTTP_OK);
    }


    /**
     * @Route("/api/raids/{id_raid}", name="get_raids_one")
     * @Method({"GET"})
     */
    public function getRaidAction(Request $request)
    {
        $raid = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->findOneBy(array("id" => $request->get('id_raid')));
        /* @var $raid Raid */

        if(empty($raid)){
            return new JsonResponse(["message" => "Le raid recherché n'est pas présent dans la BDD !"], Response::HTTP_NOT_FOUND); 
        }

        $formatted = [
            'id' => $raid->getId(),
            'nom' => $raid->getNom(),
            'date' => $raid->getDate(),
            'lieu' => $raid->getLieu(),
            'edition' => $raid->getEdition(),
            'equipe' => $raid->getEquipe()    
        ];

        return new JsonResponse($formatted, Response::HTTP_OK);
    }

    /**
     * @Route("/api/raids/{id_raid}", name="post_raids_one")
     * @Method({"POST"})
     */
    public function postRaidAction(Request $request)
    {
        
        $sn = $this->getDoctrine()->getManager();
        $raid = $this->getDoctrine()->getRepository('AppBundle:Raid')
                ->find($request->get('id_raid'));

        if(empty($raid)){
            return new JsonResponse(["message" => "Le raid recherché n'est pas présent dans la BDD !"], Response::HTTP_NOT_FOUND); 
        }

        $nom = $request->get('nom');
        $lieu = $request->get('lieu');
        $date = new \DateTime($request->get('date'));
        $edition = $request->get('edition');
        $equipe = $request->get('equipe');
        
        $raid->setNom($nom);
        $raid->setLieu($lieu);
        $raid->setDate($date);
        $raid->setEdition($edition);
        $raid->setEquipe($equipe);

        $sn->flush();

        return new JsonResponse(['message' => "Raid mise à jour avec succès !"], Response::HTTP_OK); 
    }

    /**
     * @Route("/api/raids/{id_raid}", name="delete_raids_one")
     * @Method({"DELETE"})
     */
    public function deleteRaidAction(Request $request)
    {
        
        $sn = $this->getDoctrine()->getManager();
        $raid = $this->getDoctrine()->getRepository('AppBundle:Raid')->find($request->get('id_raid'));

        if(empty($raid)){
            return new JsonResponse(["message" => "Le raid recherché n'est pas présent dans la BDD !"], Response::HTTP_NOT_FOUND); 
        }

        $sn->remove($raid);
        $sn->flush();
        
        return new JsonResponse(['message' => "Raid supprimé avec succès !"], Response::HTTP_OK); 
    }


    /**
     * @Route("/api/raids/benevoles/users/{id_user}", name="get_all_raids_benevoles")
     * @Method({"GET"})
     */
    public function getRaidsWhereBenevoleByIdUserAction(Request $request)
    {
        $benevoles = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->findBy(array(
                    "idUser" => $request->get("id_user")
                ));
        /* @var $raids Raids[] */

        if(empty($benevoles)){
            return new JsonResponse(["message" => "Cet utilisateur n'est bénévole d'aucun RAID !"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [];
        foreach ($benevoles as $benevole) {
            
            $raid = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->findOneBy(array(
                    "id" => $benevole->getIdRaid()
                ));

            $formatted[] = [
                'id' => $raid->getId(),
                'nom' => $raid->getNom(),
                'lieu' => $raid->getLieu(),
                'date' => $raid->getDate(),
                'edition' => $raid->getEdition(),
                'equipe' => $raid->getEquipe()    
            ];
        }

        return new JsonResponse($formatted, Response::HTTP_OK);
    }


    /**
     * @Route("/api/raids/benevoles/users/{id_user}", name="delete_all_raids_benevoles")
     * @Method({"DELETE"})
     */
    public function deleteRaidsWhereBenevoleByIdUserAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $benevoles = $em->getRepository('AppBundle:Benevole')
                    ->findBy(array(
                        "idUser" => $request->get('id_user')
                    ));

        if(empty($benevoles)){
            return new JsonResponse(["message" => "Cet utilisateur n'est bénévole d'aucun RAID !"], Response::HTTP_NOT_FOUND);    
        }

        foreach ($benevoles as $benevole) {
            $raid = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->findOneBy(array(
                    "id" => $benevole->getIdRaid()
                ));
            $em->remove($raid);
        }

        $em->flush();

        return new JsonResponse(["message" => "Les raids bénévoles de cet utilisateur ont été supprimés avec succès !"], Response::HTTP_OK);
    }

    /**
     * @Route("/api/raids/organisateurs/users/{id_user}", name="get_all_raids_organisateurs")
     * @Method({"GET"})
     */
    public function getRaidsWhereOrganisateurByIdUserAction(Request $request)
    {
        $organisateurs = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Organisateur')
                ->findBy(array(
                    "idUser" => $request->get("id_user")
                ));
        /* @var $raids Raids[] */

        if(empty($organisateurs)){
            return new JsonResponse(["message" => "Cet utilisateur n'est organisateur d'aucun RAID !"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [];
        foreach ($organisateurs as $organisateur) {
            
            $raid = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->findOneBy(array(
                    "id" => $organisateur->getIdRaid()
                ));

            $formatted[] = [
                'id' => $raid->getId(),
                'nom' => $raid->getNom(),
                'lieu' => $raid->getLieu(),
                'date' => $raid->getDate(),
                'edition' => $raid->getEdition(),
                'equipe' => $raid->getEquipe()    
            ];
        }

        return new JsonResponse($formatted, Response::HTTP_OK);
    }


    /**
     * @Route("/api/raids/organisateurs/users/{id_user}", name="delete_all_raids_organisateurs")
     * @Method({"DELETE"})
     */
    public function deleteRaidsWhereOrganisateurByIdUserAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $organisateurs = $em->getRepository('AppBundle:Organisateur')
                    ->findBy(array(
                        "idUser" => $request->get('id_user')
                    ));

        if(empty($organisateurs)){
            return new JsonResponse(["message" => "Cet utilisateur n'est organisateur d'aucun RAID !"], Response::HTTP_NOT_FOUND);    
        }

        foreach ($organisateurs as $organisateur) {
            $raid = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->findOneBy(array(
                    "id" => $organisateur->getIdRaid()
                ));
            $em->remove($raid);
        }

        $em->flush();

        return new JsonResponse(["message" => "Les raids bénévoles de cet utilisateur ont été supprimés avec succès !"], Response::HTTP_OK);
    }


    /**
     * @Route("/api/raids/parcours/{id_parcours}", name="get_all_raids_parcours")
     * @Method({"GET"})
     */
    public function getRaidsByIdParcoursAction(Request $request)
    {
        $parcours = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Parcours')
                ->findBy(array(
                    "id" => $request->get("id_parcours")
                ));
        /* @var $raids Raids[] */

        if(empty($parcours)){
            return new JsonResponse(["message" => "Ce parcours n'est pas présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        $formatted = [];
        foreach ($parcours as $parcour) {
            
            $raid = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->findOneBy(array(
                    "id" => $parcour->getIdRaid()
                ));

            $formatted[] = [
                'id' => $raid->getId(),
                'nom' => $raid->getNom(),
                'lieu' => $raid->getLieu(),
                'date' => $raid->getDate(),
                'edition' => $raid->getEdition(),
                'equipe' => $raid->getEquipe()    
            ];
        }

        return new JsonResponse($formatted, Response::HTTP_OK);
    }


    /**
     * @Route("/api/raids/parcours/{id_parcours}", name="delete_all_raids_parcours")
     * @Method({"DELETE"})
     */
    public function deleteRaidsByIdParcoursAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $parcours = $em->getRepository('AppBundle:Parcours')
                    ->findBy(array(
                        "id" => $request->get('id_parcours')
                    ));

        if(empty($parcours)){
            return new JsonResponse(["message" => "Cet utilisateur n'est organisateur d'aucun RAID !"], Response::HTTP_NOT_FOUND);    
        }

        foreach ($parcours as $parcour) {
            $raid = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->findOneBy(array(
                    "id" => $parcour->getIdRaid()
                ));
            $em->remove($raid);
        }

        $em->flush();

        return new JsonResponse(["message" => "Les raids organisateurs de cet utilisateur ont été supprimés avec succès !"], Response::HTTP_OK);
    }
}