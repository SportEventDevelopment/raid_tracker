<?php
 
namespace AppBundle\Listener;
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
 
class LogoutListener implements LogoutHandlerInterface
{
    protected $rest;

    public function __construct(\AppBundle\Service\RestClient $rest)  {
        $this->rest = $rest;
    }
    /**
     * @{inheritDoc}
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $idtoken = $token->getUser()->getIdToken();
        $token = $token->getUser()->getToken();

        $url = 'api/auth-tokens/'.$idtoken;
        $deleteToken = $this->rest->delete($url, $token);
    }
}