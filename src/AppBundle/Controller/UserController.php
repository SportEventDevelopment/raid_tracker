<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/api/users", name="get_all_users")
     * @Method({"GET"})
     */
    public function getUsersAction(Request $request)
    {
        $users = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->findAll();
        
        if(empty($users)){
            return new JsonResponse(["message" => "Aucun utilisateur présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        /* @var $users users */
        $formatted = [];
        foreach ($users as $user) {
            $formatted[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'role' => $user->getRole(),
                'password ' => $user->getPassword()
            ];
        }

        return new JsonResponse($formatted, Response::HTTP_OK);
    }

    /**
     * @Route("/api/users", name="post_all_users")
     * @Method({"POST"})
     */
    public function postUsersAction(Request $request)
    {
        $request_json = json_decode($request->getContent());

        $user = new User();
        $em = $this->get('doctrine.orm.entity_manager');

        $user->setEmail($request->request->get('email'));
        $user->setName($request->request->get('name'));
        $user->setRole($request->request->get('role'));
        $user->setPlainPassword($request->request->get('password'));

        // Encode the new users password
        $encoder = $this->get('security.password_encoder');
        $password = $encoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        // Save
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Utilisateur ajouté !'], Response::HTTP_OK);
    }

    /**
     * @Route("/api/users", name="delete_all_users")
     * @Method({"DELETE"})
     */
    public function deleteUsersAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('AppBundle:User')->findAll();

        foreach ($users as $user) {
            $em->remove($user);
        }

        $em->flush();

        return new JsonResponse(["message" => "Les utilisateurs ont ete supprimes avec succes !"], Response::HTTP_OK);
    }


    /**
     * @Route("/api/users/{id_user}", name="get_users_one")
     * @Method({"GET"})
     */
    public function getUserAction(Request $request)
    {
        $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id_user'));
        /* @var $user User */

        if(empty($user)){
            return new JsonResponse(["message" => "Utilisateur non trouvé !", Response::HTTP_NOT_FOUND]);
        }
        
        $formatted = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'role' => $user->getRole(),
            'password ' => $user->getPassword()
        ];

        return new JsonResponse($formatted, Response::HTTP_OK);
    }


    /**
     * @Route("/api/users/{id_user}", name="post_users_one")
     * @Method({"POST"})
     */
    public function postUserAction(Request $request)
    {
        
        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->get('id_user'));

        $name = $request->get('name');
        $email = $request->get('email');
        $role = $request->get('role');
        $password = $request->get('password');

        if (empty($user)) {
            return new JsonResponse(['message' => "Utilisateur non trouvé !"], Response::HTTP_NOT_FOUND);
        }
        
        $user->setName($name);
        $user->setEmail($email);
        $user->setRole($role);

        if($password !== $user->getPassword()){         
            // Encode the new users password
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $password);
            $user->setPassword($password);
        }

        $sn->flush();

        return new JsonResponse(['message' => "Utilisateur mise à jour avec succès !"], Response::HTTP_OK); 
    }

    /**
     * @Route("/api/users/{id_user}", name="delete_users_one")
     * @Method({"DELETE"})
     */
    public function deleteUserAction(Request $request)
    {
        
        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->get('id_user'));

        if(empty($user)){
            return new JsonResponse(["message" => "L'utilisateur recherché n'est pas présent dans la BDD !"], Response::HTTP_NOT_FOUND); 
        }

        $sn->remove($user);
        $sn->flush();
        
        return new JsonResponse(['message' => "Utilisateur supprimé avec succès !"], Response::HTTP_OK); 
    }

}
