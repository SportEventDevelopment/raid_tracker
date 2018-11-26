<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Benevole;
use AppBundle\Entity\UserRegistration;

use AppBundle\Entity\PrefPoste;
use AppBundle\Entity\User;



class BenevoleController extends Controller
{

    /**
     * Creates a new parcour entity.
     * @Route("/benevole/{id_raid}/inviter", name="inviter_benevole")
     */
    public function inviterBenevoles(Request $request, $id_raid)
    {
        $user = new UserRegistration();
        $form = $this->createForm('AppBundle\Form\InviterBenevoleType',$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            return $this->redirectToRoute('landing');
        }

        return $this->render('landing/inviterBenevole.html.twig', array(
            'user' =>$this->getUser(),
            'form' => $form->createView()
        ));
    }

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
     * Creates a new parcour entity.
     *
     * @Route("/benevole/{id_user}/{id_raid}/new", name="rejoindre_raid_comme_benevole")
     * @Method({"GET", "POST"})
     */
    public function rejoindreRaidBenevole(Request $request,$id_user, $id_raid)
    {

      $benevole = new Benevole();

      $benevole->setIdUser($id_user);
      $benevole->setIdRaid($id_raid);
      $benevole->setEstBenevole(false);

        $PrefPoste = new PrefPoste();
        $form = $this->createForm('AppBundle\Form\PrefPosteType', $PrefPoste);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $PrefPoste->setIdRaid($id_raid);
            $PrefPoste->setIdUser($id_user);

            $em->persist($PrefPoste);
            $em->persist($benevole);

            $em->flush();
            return $this->redirectToRoute('landing');

          //  return $this->redirectToRoute('parcours_show', array('id' => $parcour->getId()));
        }

        return $this->render('landing/rejoindreRaid.html.twig', array(
            'form' => $form->createView(),
            'user' =>$this->getUser()
        ));
    }

    /**
     * Displays a form to edit an existing parcour entity.
     *
     * @Route("/user/{iduser}/raid/{idraid}/choixOrga", name="choix_bene_defi")
     * @Method({"GET"})
     */
    public function ChoixDefinitifBenevoleAction(Request $request,$iduser,$idraid)
    {

      $em = $this->getDoctrine()->getManager();

      $benevole = $this->getDoctrine()->getManager()
              ->getRepository('AppBundle:Benevole')
              ->findBenevoleByIdRaid($request->get('idraid'),$request->get('iduser'));
              $benevole->setEstBenevole(true);
              $em->persist($benevole);
              $em->flush();

          $raids_organisateurs = $this->get('doctrine.orm.entity_manager')
                                    ->getRepository('AppBundle:Raid')
                                    ->findRaidsOrganisateursByIdUser($this->getUser()->getId());

            return $this->render('landing/adminBenevole.html.twig', array(
                  'user' => $this->getUser(),
                  'raids_organisateurs' =>$raids_organisateurs
              ));
    }

    /**
     * Creates a new parcour entity.
     *
     * @Route("/benevole/{id_raid}/inviter", name="inviter_benevole")
     * @Method({"GET", "POST"})
     */
    public function inviterBenevoleDansRaid(Request $request, $id_raid,\Swift_Mailer $mailer)
    {


      //  $PrefPoste = new PrefPoste();

        $user = new User();
        $form = $this->createForm('AppBundle\Form\InviterBenevoleType',$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
     ;
    $this->get('mailer')->send($message);

          //  var_dump($form->getEmail());die();

        //    var_dump($user->getEmail());die();
          /*  $PrefPoste->setIdRaid($id_raid);

            $em->persist($PrefPoste);
            $em->persist($benevole);

            $em->flush();*/
            return $this->redirectToRoute('landing');

          //  return $this->redirectToRoute('parcours_show', array('id' => $parcour->getId()));
        }

        return $this->render('landing/inviterBenevole.html.twig', array(
            'form' => $form->createView(),
            'user' =>$this->getUser()
        ));
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
