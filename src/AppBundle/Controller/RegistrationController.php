<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
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
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // ****************
            // Insert new user
            // ****************
            $headers = array('Accept' => 'application/json');
            $data = $request->request->get('appbundle_raid');
            
            //remove the token
            array_pop($data);
            $body = Unirest\Request\Body::form($data);
            $response = Unirest\Request::post('http://raidtracker.ddns.net/raid_tracker_api/web/app.php/api/users', $headers, $body);
            var_dump($response);die;
            return $this->forward('AppBundle\Controller\SecurityController::loginAction');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
