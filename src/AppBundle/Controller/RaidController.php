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
use AppBundle\Entity\Parcours;
use \Unirest;

/**
 * Raid controller.
 */
class RaidController extends Controller
{
    /**
     * @Route("/raids/create", name="create_raid")
     */
    public function createRaid(Request $request)
    {
        $raid = new Raid();
        $form = $this->createForm('AppBundle\Form\RaidType', $raid);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $raid_data =$this->get('app.serialize')->entityToArray($form->getData());
            $date = $form->getData()->getDate()->format('Y/m/d H:i');

            if($date > date("Y/m/d H:i")) {

                $raid_data['date'] = $date;
                if(!$raid_data['visibility']){
                    array_pop($raid_data);
                }

                $response = $this->get('app.restclient')->post(
                    'api/raids',
                    $raid_data,
                    $this->getUser()->getToken()
                );

                if($response){

                    $organisateur_data = array(
                        'idUser' => $this->getUser()->getIdUser(),
                        'idRaid' => $response->body->id
                    );
                    $response = $this->get('app.restclient')->post(
                        'api/organisateurs/raids/'. $organisateur_data['idRaid'].'/users/'. $organisateur_data['idUser'],
                        $organisateur_data,
                        $this->getUser()->getToken()
                    );

                    if($response){
                        $this->addFlash('success','RAID créé avec succès !');
                    } else {
                        $this->addFlash('error',"Erreur lors de l'enregistrement de l'organisateur");
                    }
                }
            }
            else {
                $this->addFlash('error','Vous ne pouvez pas créer un RAID dans le passé...');
                return $this->redirectToRoute('create_raid');
            }

            return $this->redirectToRoute('landing');
        }

        return $this->render('raid/new.html.twig', array(
            'user' => $this->getUser(),
            'raid' => $form->createView(),
        ));
    }

    /**
     * @Route("/raids/{id}/", name="edit_raid")
     */
    public function descriptionRaidOrganisateur(Request $request)
    {
        $url = 'api/raids/'.$request->get('id');
        $raid = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $url = 'api/organisateurs/raids/'.$request->get('id').'/users/'. $this->getUser()->getIdUser();
        $est_organisateur = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $url = 'api/parcours/raids/'.$request->get('id');
        $all_parcours = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $url = 'api/repartitions/raids/'.$request->get('id');
        $all_repartitions = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        return $this->render('raid/description_raid_organisateur.html.twig', array(
            'user' => $this->getUser(),
            'est_organisateur' =>$est_organisateur,
            'raid' => $raid,
            'all_parcours' => $all_parcours,
            'all_repartitions' => $all_repartitions,
            'token' => $this->getUser()->getToken()
        ));
    }


    /**
     * @Route("/raids/benevoles/{idbenevole}/postes/{idposte}", name="choix_bene_defi")
     */
    public function ChoixDefinitifBenevole(Request $request)
    {
        // Ajout de la répartition du poste du bénévole
        $repartition = array(
            'idPoste' => $request->get('idposte'),
            'idBenevole' => $request->get('idbenevole')
        );
        $request = $this->get('app.restclient')->post(
            'api/repartitions',
            $repartition,
            $this->getUser()->getToken()
        );
        return $this->redirectToRoute('landing');
    }


    /**
     * @Route("/raids/benevole/{id_raid}", name="postes_benevole_raid")
     */
    public function editRaidBenevole(Request $request)
    {
        $posteRepartis = true;
        $url = 'api/raids/'.$request->get('id_raid');
        $raid = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $url = 'api/repartitions/raids/'.$request->get('id_raid').'/users/'.$this->getUser()->getIdUser();
        $all_repartitions = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        if($all_repartitions == null) {
          $posteRepartis = false;
        }

        $url = 'api/prefpostes/raids/'. $request->get('id_raid') .'/users/'. $this->getUser()->getIdUser();
        $all_prefpostes = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        return $this->render('raid/postes_benevoles.html.twig', array(
            'user' => $this->getUser(),
            'all_repartitions' => $all_repartitions,
            'all_prefpostes' => $all_prefpostes,
            'raid' => $raid,
            'token' => $this->getUser()->getToken(),
            'posteRepartis' => $posteRepartis
        ));
    }



}
