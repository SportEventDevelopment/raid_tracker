<?php
namespace AppBundle\Controller;

use AppBundle\Entity\UserRegistration;
use AppBundle\Form\UserRegistrationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request)
    {
        // Create a new blank user and process the form
        $user = new UserRegistration();
        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

          $data =$this->get('app.serialize')->entityToArray($form->getData());

          $mdp = $data['plainPassword'];

          if( (strlen($mdp) <8) || (!(preg_match("~[0-9]~", $mdp))) || (!(preg_match("~[A-Z]~", $mdp)))){
            $this->addFlash('error','Au moins 8 charactères dont 1 chiffre et 1 majuscule !');
          }
          else {
            $response = $this->get('app.restclient')->post('api/users', $data);
              if($response) {
                $this->addFlash('success','Votre compte a été bien créé !');
                return $this->redirectToRoute('login');
              }
            else {
              $this->addFlash('error','Erreur lors de la création de compte !');
              return $this->render('auth/register.html.twig', [
                  'form' => $form->createView()
              ]);
            }
          }
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
             'mdpPasCorrect' => true
        ]);
    }
}
