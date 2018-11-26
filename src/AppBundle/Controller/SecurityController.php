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
        $url = 'api/raids/visible/all';
        $raids_visibles = $this->get('app.restclient')->get($url);

        return $this->render(
           'auth/login.html.twig',
            array(
                'last_username' => $authenticationUtils->getLastUsername(),
                'error'         => $authenticationUtils->getLastAuthenticationError(),
                'raids_visibles'=> $raids_visibles
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
        throw new \RuntimeException('Michel, active le logout dans le pare-feu...');
    }
}
