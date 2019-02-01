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
use \Unirest;

/**
 * Gestion controller.
 */
class GestionController extends Controller
{

    /**
     * @Route("/gestion/{id_raid}/description", name="gestion_raid_description")
     */
    public function gestionRaidDescription(Request $request)
    {
        $raid_data = null;
        $url = 'api/raids/'.$request->get('id_raid');
        $raid_data = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $est_organisateur = null;
        $url = 'api/organisateurs/raids/'.$request->get('id_raid').'/users/'. $this->getUser()->getIdUser();
        $est_organisateur = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $raid = null;
        if($raid_data != null){
            $raid = new Raid();
            $raid->setEquipe($raid_data->equipe);
            $raid->setDate(new \DateTime($raid_data->date));
            $raid->setNom($raid_data->nom);
            $raid->setLieu($raid_data->lieu);
            $raid->setEdition($raid_data->edition);
            $raid->setVisibility($raid_data->visibility);
        }

        $editForm = $this->createForm('AppBundle\Form\RaidType', $raid);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $url = 'api/raids/'.$request->get('id_raid');
            $t_raid =$this->get('app.serialize')->entityToArray($raid);
            $date = $editForm->getData()->getDate()->format('Y/m/d H:i');
            $t_raid['date'] = $date;

            if ($t_raid['visibility'] == false){
                unset($t_raid['visibility']);
            }
            $edit_raid = $this->get('app.restclient')->post($url, $t_raid, $this->getUser()->getToken());

            return $this->redirectToRoute('landing_gerer_raid');
        }

        return $this->render('raid/edit.html.twig', array(
            'user'=>$this->getUser(),
            'est_organisateur' => $est_organisateur,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * @Route("/gestion/{id_raid}/organisateurs", name="gestion_raid_organisateurs")
     */
    public function gestionRaidOrganisateurs(Request $request)
    {
        $est_organisateur = null;
        $url = 'api/organisateurs/raids/'.$request->get('id_raid').'/users/'. $this->getUser()->getIdUser();
        $est_organisateur = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $url = 'api/organisateurs/raids/'.$request->get('id_raid');
        $organisateurs = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());
            $orgaEmail = [];
                foreach ($organisateurs as $key => $value) {
                array_push($orgaEmail,$value->idUser->email);
                }

        $form = $this->createForm('AppBundle\Form\InviterBenevoleType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $url_email = 'api/users/emails/'.$form->getData()->getEmail();
            if (in_array($form->getData()->getEmail(), $orgaEmail)) {
              return $this->render('gestion/edit_organisateurs.html.twig', array(
                  'user'=>$this->getUser(),
                  'est_organisateur' => $est_organisateur,
                  'organisateurs' => $organisateurs,
                  'errors' => "L'email rentré est déja organisateur de ce raid.",
                  'form' => $form->createView()
      ));
      }
        else {
          $userSearch = $this->get('app.restclient')->get($url_email, $this->getUser()->getToken());
          if(empty($userSearch)){
              return $this->render('gestion/edit_organisateurs.html.twig', array(
                  'user'=>$this->getUser(),
                  'est_organisateur' => $est_organisateur,
                  'organisateurs' => $organisateurs,
                  'errors' => "L'utilisateur que vous souhaitez ajouter n'existe pas!",
                  'form' => $form->createView()
              ));
          }

          $url = 'api/organisateurs/raids/'.$request->get('id_raid').'/users/'.$userSearch->id;
          $orga_data = array(
              'idUser' => $userSearch->id,
              'idRaid' => $request->get('id_raid')
          );

          $new_orga = $this->get('app.restclient')->post($url, $orga_data, $this->getUser()->getToken());
          if(empty($new_orga)){
              return $this->render('gestion/edit_organisateurs.html.twig', array(
                  'user'=>$this->getUser(),
                  'est_organisateur' => $est_organisateur,
                  'organisateurs' => $organisateurs,
                  'errors' => "Problème rencontré lors de l'enregistrement du nouvel organisateur",
                  'form' => $form->createView()
              ));
          }
          else {
            $this->addFlash('notice',"L'utilisateur a été bien ajouté comme organisateur!");
          }

          return $this->redirectToRoute('gestion_raid_organisateurs', array('id_raid' => $request->get('id_raid')));
        }
      }

        return $this->render('gestion/edit_organisateurs.html.twig', array(
            'user'=>$this->getUser(),
            'est_organisateur' => $est_organisateur,
            'organisateurs' => $organisateurs,
            'errors' => null,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/gestion/{id_raid}/organisateurs/{id_user}/remove", name="gestion_raid_remove_organisateur")
     */
    public function gestionRaidOrganisateursRemove(Request $request)
    {
        $id_raid = $request->get('id_raid');
        $id_user = $request->get('id_user');

        $url = 'api/organisateurs/raids/'.$request->get('id_raid').'/users/'. $this->getUser()->getIdUser();
        $est_organisateur = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $url_raids = 'api/organisateurs/raids/'.$id_raid;
        $organisateurs = $this->get('app.restclient')
            ->get($url_raids, $this->getUser()->getToken());

        if(count($organisateurs) == 1){
            return $this->redirectToRoute('gestion_raid_organisateurs', array(
                'id_raid' => $request->get('id_raid')
            ));
        }

        $url = 'api/organisateurs/raids/'.$id_raid.'/users/'.$id_user;
        $organisateur = $this->get('app.restclient')
            ->delete($url, $this->getUser()->getToken());

        return $this->redirectToRoute('gestion_raid_organisateurs', array('id_raid' => $request->get('id_raid')));
    }

}
