<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class WebserviceUser implements UserInterface
{
    
    private $idToken;
    private $token;
    private $email;
    private $username;
    private $idUser;
    private $salt;
    private $roles;

    public function __construct($idToken, $token, $email, $username, $idUser, $salt, array $roles)
    {
        $this->idToken = $idToken;
        $this->token = $token;
        $this->email = $email;
        $this->username = $username;
        $this->idUser = $idUser;
        $this->salt = $salt;
        $this->roles = $roles;
    }
    
    public function getIdToken()
    {
        return $this->idToken;
    }
    
    public function getToken()
    {
        return $this->token;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function getPassword(){
        return null;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {}

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            return false;
        }

        if ($this->idToken !== $user->getIdToken()) {
            return false;
        }

        if ($this->token !== $user->getToken()) {
            return false;
        }

        if ($this->email !== $user->getEmail()) {
            return false;
        }
        
        if ($this->username !== $user->getUsername()) {
            return false;
        }
       
        if ($this->idUser !== $user->getIdUser()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        return true;
    }
}