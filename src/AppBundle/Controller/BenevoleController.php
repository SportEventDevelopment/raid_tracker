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
     * Creates a new parcour entity.
     *
     * @Route("/benevoles/raids/{id_raid}/invitations", name="inviter_benevole")
     */
    public function inviterBenevoleDansRaid(Request $request, \Swift_Mailer $mailer)
    {
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

        return $this->render('landing/inviterBenevole.html.twig', array(
            'form' => $form->createView(),
            'user' =>$this->getUser()
        ));
    }
}