<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class WebserviceUser implements UserInterface
{
    private $email;
    private $password;
    private $username;
    private $salt;
    private $roles;

    public function __construct($email, $password, $username, $salt, array $roles)
    {
        $this->email = $email;
        $this->password = $password;
        $this->username = $username;
        $this->salt = $salt;
        $this->roles = $roles;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
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

        if ($this->email !== $user->getEmail()) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }
        
        if ($this->username !== $user->getUsername()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        return true;
    }
}