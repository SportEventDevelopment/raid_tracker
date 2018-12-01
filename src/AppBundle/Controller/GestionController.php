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
        $url = 'api/raids/'.$request->get('id_raid');
        $raid_data = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $raid = new Raid();
        $raid->setEquipe($raid_data->equipe);
        $raid->setDate(new \DateTime($raid_data->date));
        $raid->setNom($raid_data->nom);
        $raid->setLieu($raid_data->lieu);
        $raid->setEdition($raid_data->edition);
        $raid->setVisibility($raid_data->visibility);

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
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * @Route("/gestion/{id_raid}/organisateurs", name="gestion_raid_organisateurs")
     */
    public function gestionRaidOrganisateurs(Request $request)
    {
        $url = 'api/organisateurs/raids/'.$request->get('id_raid');
        $organisateurs = $this->get('app.restclient')
            ->get($url, $this->getUser()->getToken());

        $editForm = $this->createForm('AppBundle\Form\InviterBenevoleType');
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
            'edit_form' => $editForm->createView()
        ));
    }
}