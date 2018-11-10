<?php

namespace AppBundle\Security\User;

use AppBundle\Service\RestClient;
use AppBundle\Entity\WebserviceUser;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;


class WebserviceAuthenticator extends AbstractFormLoginAuthenticator
{
    private $container;
    private $restClient;

    public function __construct(ContainerInterface $container, RestClient $restClient)
    {
        $this->container = $container;
        $this->restClient = $restClient;
    }

    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() != '/login_check' || $request->getMethod() != 'POST') {
            return;
        }

        $username = $request->request->get('_email');
        $request->getSession()->set(Security::LAST_USERNAME, $username);
        $password = $request->request->get('_password');

        return array(
            'username' => $username,
            'password' => $password
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (array_key_exists('username', $credentials) == false) {
            return null;
        }
        $email = $credentials['username'];
        $password = $credentials['password'];
        if ($email == '') {
            return null;
        }

        $response = $this->restClient->IsValidLogin($email, $password);
        if ($response) {
            return new WebserviceUser($email, $response->user->username, null, ['ROLE_USER']);
        } else {
            throw new CustomUserMessageAuthenticationException('Identifiant ou mot de passe incorrect');
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // AJAX! Return some JSON
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => $exception->getMessageKey()), 403);
        }

        // for non-AJAX requests, return the normal redirect
        return parent::onAuthenticationFailure($request, $exception);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // AJAX! Return some JSON
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('userId' => $token->getUser()->getId()));
        }

        // for non-AJAX requests, return the normal redirect
        return parent::onAuthenticationSuccess($request, $token, $providerKey);
    }

    protected function getLoginUrl()
    {
        return $this->container->get('router')->generate('login');
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->container->get('router')->generate('landing');
    }
}