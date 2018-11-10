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
            
            var_dump($data);die;
            $data = Unirest\Request\Body::form($data);
            $response = $this->get('app.restclient')->post('api/users', $data);
            return $this->forward('AppBundle\Controller\SecurityController::login');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
