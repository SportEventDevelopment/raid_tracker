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
     * @Route("/benevoles/raids/{id_raid}/join", name="rejoindre_raid_comme_benevole")
     */
    public function rejoindreRaidBenevole(Request $request)
    {
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

        if($response){
            $this->addFlash("success", "Vous avez rejoint le RAID en tant que bénévoles avec succès!");
        } else {
            $this->addFlash("error", "Il semble qu'une erreur soit survenue lorsque vous avez essayé de rejoindre un RAID. Essayez de nouveau");
        }

        return $this->redirectToRoute('landing');
    }

    /**
     *
     * @Route("/preferences/benevoles/raids/{id_raid}", name="ajouter_preference")
     */
    public function preferenceBenevole(Request $request)
    {
        $posteNonExistant = false;
        $url = 'api/postes/raids/'.$request->get('id_raid').'/available';
        $postes_availables = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        if($postes_availables == null){
          $posteNonExistant = true;
        }

        $PrefPoste = new PrefPoste();
        $form = $this->createForm('AppBundle\Form\PrefPosteType', $PrefPoste, array(
            'postes_disponibles' => $postes_availables
        ));

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $url = 'api/benevoles/raids/'.$request->get('id_raid').'/users/'. $this->getUser()->getIdUser();
            $benevole = $this->get('app.restclient')
                ->get($url, $this->getUser()->getToken());

            if($benevole) {
                $url = 'api/prefpostes/postes/'. $form->getData()->getIdPoste()->id .'/users/'. $this->getUser()->getIdUser();
                $prefposte_search = $this->get('app.restclient')->get($url, $this->getUser()->getToken());
                
                if(empty($prefposte_search)){
                    // Ajout de la préférence du poste du bénévole
                    $prefposte = array(
                        'idPoste' => $form->getData()->getIdPoste()->id,
                        'idBenevole' => $benevole->id,
                        'priority' => 0
                    );
                    
                    $reponse = $this->get('app.restclient')->post(
                        'api/prefpostes',
                        $prefposte,
                        $this->getUser()->getToken()
                    );
        
                    if($reponse){
                        $this->addFlash("success", "Votre préférence a bien été ajoutée");
                    } else {
                        $this->addFlash("error", "Erreur d'enregistrement dans la base de données de votre préférence");
                    }
                } else {
                    $this->addFlash("error", "Le choix de ce poste est déjà dans votre liste de préférence !");
                }
            }
            
            return $this->redirectToRoute('postes_benevole_raid', ['id_raid' => $request->get('id_raid')]);
        }

        return $this->render('landing/add_preference.html.twig', array(
            'form' => $form->createView(),
            'user' =>$this->getUser(),
            'posteNonExistant' => $posteNonExistant,
        ));
    }

    /**
     *
     * @Route("/benevoles/raids/{id_raid}/invitations", name="inviter_benevole")
     */
    public function inviterBenevoleDansRaid(Request $request, \Swift_Mailer $mailer)
    {
        $est_organisateur = null;
        $url = 'api/organisateurs/raids/'.$request->get('id_raid').'/users/'. $this->getUser()->getIdUser();
        $est_organisateur = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());
            
        $form = $this->createForm('AppBundle\Form\InviterBenevoleType');
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {

            $message = (new \Swift_Message('Invitation pour bénévolat raid'))
                ->setFrom('sporteventdevelopment@gmail.com')
                ->setTo($form->getData()->getEmail())
                ->setBody(
                    $this->renderView('gestion/invitationRaid.html.twig',
                        array('link' => 'http://raidtracker.ddns.net/raid_tracker/web/app.php/benevoles/raids/'.$request->get('id_raid').'/join')),
                    'text/html'
                );

            $mailer->send($message);
            return $this->redirectToRoute('inviter_benevole', array('id_raid' => $request->get('id_raid')) );
        }

        return $this->render('landing/inviter_benevole.html.twig', array(
            'est_organisateur' => $est_organisateur,
            'user' =>$this->getUser(),
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/benevoles/guide/{id_poste}", name="benevole_guide")
     */
    public function guiderBenevolePoste(Request $request)
    {
        $url = 'api/postes/'.$request->get('id_poste');
        $poste = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        if($poste){
            $url = 'api/points/'.$poste->body->idPoint;
            $point = $this->get('app.restclient')
                ->get($url, $this->getUser()->getToken());
              
            return $this->redirect('https://www.google.com/maps/dir/'. $point->response->lon .','. $point->response->lat .'/48.814614,-3.4563867');
        }

    }

    /**
     *
     * @Route("/benevoles/checkin", name="benevole_checkin")
     */
    public function confirmerPresenceBenevole(Request $request)
    {

    }
}
