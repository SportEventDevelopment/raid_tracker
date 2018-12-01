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
     * Creates a new parcour entity.
     *
     * @Route("/benevoles/raids/{id_raid}", name="rejoindre_raid_comme_benevole")
     * @Method({"GET", "POST"})
     */
    public function rejoindreRaidBenevole(Request $request)
    {
        $posteNonExistant = false;
        $url = 'api/postes/raids/'.$request->get('id_raid').'/available';
        $postes_availables = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());
        if($postes_availables == null){
          $posteNonExistant = true;
        }
        //var_dump($postes_availables);die();
        $PrefPoste = new PrefPoste();
        $form = $this->createForm('AppBundle\Form\PrefPosteType', $PrefPoste, array(
            'postes_disponibles' => $postes_availables
        ));


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Ajout bénévole
            $benevole = array(
                'idUser' => $this->getUser()->getIdUser(),
                'idRaid' => $request->get('id_raid')
            );
            $response = $this->get('app.restclient')->post(
                'api/benevoles/raids/'.$benevole['idRaid'].'/users/'.$benevole['idUser'],
                $benevole,
                $this->getUser()->getToken()
            );

            // Ajout de la préférence du poste du bénévole
            $prefposte = array(
                'idPoste' => $form->getData()->getIdPoste()->id,
                'idBenevole' => $response->body->id
            );
            $request = $this->get('app.restclient')->post(
                'api/prefpostes',
                $prefposte,
                $this->getUser()->getToken()
            );

            return $this->redirectToRoute('landing');
        }

        return $this->render('landing/rejoindreRaid.html.twig', array(
            'form' => $form->createView(),
            'user' =>$this->getUser(),
            'posteNonExistant' => $posteNonExistant,
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
        $user = new User();
        $form = $this->createForm('AppBundle\Form\InviterBenevoleType',$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $this->get('mailer')->send($message);
            return $this->redirectToRoute('landing');
        }

        return $this->render('landing/inviterBenevole.html.twig', array(
            'form' => $form->createView(),
            'user' =>$this->getUser()
        ));
    }
}
