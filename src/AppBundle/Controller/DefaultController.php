<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

    //  $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->getUtilisateur($this->getUser()->getUsername())
        // replace this example code with whatever you need
if($this->getUser() ===null)  {
  return $this->render('default/index.html.twig'
/*   [
      'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
  ] */

);

}
else {

return $this->render('default/index2.html.twig',
 array('user' => $this->getUser()->getName(),
)
);
}

    }

    /**
     * @Route("/sucess", name="success")
     */
public function ConnexionReussieAction(){
  return $this->render('default/index2.html.twig',
   array('user' => $this->getUser()->getName(),
  )
  );
}



    /**
     * @Route("/login", name="login")
     */
/*    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
       return $this->render(
           'auth/login.html.twig',
           array(
               'last_username' => $authenticationUtils->getLastUsername(),
               'error'         => $authenticationUtils->getLastAuthenticationError(),
           )
       );
    }*/




}
