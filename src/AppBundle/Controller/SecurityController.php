<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @Route("/", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
       return $this->render(
           'auth/login.html.twig',
           array(
               'last_username' => $authenticationUtils->getLastUsername(),
               'error'         => $authenticationUtils->getLastAuthenticationError(),
           )
       );
    }
    
    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheck(Request $request){}

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(Request $request){
        // $url = 'api/auth-tokens/'.$this->getUser()->getIdToken();
        // $deleteToken = $this->get('app.restclient')->delete($url);
        // var_dump($deleteToken);die;
    }
}
